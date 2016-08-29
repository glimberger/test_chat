<?php

namespace AppBundle\Model;

use AppBundle\Entity\ChatConnection;
use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\ChatRoom;
use AppBundle\Entity\ChatUser;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    const MSG_SUBSCRIBE     = "subscribe";
    const MSG_SUBSCRIBE_ACK = 'subscribe_acknowledge';
    const MSG_PUBLISH       = 'publish';
    const MSG_PUBLISH_ACK   = 'publish_acknowledge';
    const MSG_JOINED        = 'joined';
    const MSG_LEFT          = 'left';
    const MSG_BEGIN_TYP     = 'begin_typing';
    const MSG_END_TYP       = 'end_typing';
    const MSG_MESSAGE       = 'message';

    const DEBUG = true;

    /**
     * Clients set.
     * A client represents a single connection (a Websocket decorator) to a client's browser.
     * There could be several clients for one user.
     *
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * Chat service.
     * @var ChatService
     */
    protected $service;

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        $this->service = new ChatService();
        $this->service->processPersistedMessages();
    }


    /**
     * @param ChatConnection $connection Original emitter connection
     * @param array          $messageToUsers
     * @param array|null     $messageToEmitter
     */
    private function sendMessage(ChatConnection $connection, $messageToUsers, $messageToEmitter = null)
    {
        if (self::DEBUG) echo "===> BEGIN sendMessage\n";

        // encode return messages
        $jsonMsg = json_encode($messageToUsers);
        $jsonMsgToEmit = json_encode($messageToEmitter);

        // send message to users
        /** @var ChatConnection $conn */
        foreach ($connection->getRoom()->getConnections() as $conn) {
            $client = $conn->getConnection($this->clients);
            if ($connection->getUser()->getId() != $conn->getUser()->getId() ) {
                // send to other users clients
                echo "\t{$messageToUsers['type']} message send to {$conn->getId()}\n";
                $client->send($jsonMsg);
            } else {
                if ($messageToEmitter != null) {
                    // send to emitter clients
                    echo "\t{$messageToEmitter['type']} message send to {$conn->getId()}\n";
                    $conn->send($jsonMsgToEmit);
                }
            }
        }

        if (self::DEBUG) echo "END sendMessage <===\n\n";
    }

    /**
     * @param $from
     * @param $message
     */
    private function dispatchMessage($from, $message)
    {
        if (self::DEBUG) echo "===> BEGIN dispatchMessage\n";

        $em = $this->service->getEntityManager();

        // chat room
        $chatRoom = $em->find('AppBundle\Entity\ChatRoom', $message['room']['id']);
        if (!$chatRoom) {
            $chatRoom = new ChatRoom();
            $chatRoom->setId($message['room']['id']);
            $em->persist($chatRoom);
        }
        if (self::DEBUG) echo "\tChatroom ID : {$chatRoom->getId()}\n";

        // user
        $emitter = $em->find('AppBundle\Entity\ChatUser', $message['emitter']['address']);
        if ($emitter) {
//            if (!$emitter->getRooms()->contains($chatRoom)) {
//                throw new \RuntimeException("Inconsistency error : the chat room declared in the message n° {$message['id']} is not the one db-related to the emitter");
//            }
        } else {
            $emitter = new ChatUser();
            $emitter
                ->setId($message['emitter']['address'])
                ->setUsername($message['emitter']['username'])
                ->setFullname($message['emitter']['fullname']);
            $em->persist($emitter);
        }
        if (self::DEBUG) echo "\tEmitter ID : {$emitter->getId()}\n";

        // connection
        $chatConnection = $em->find('AppBundle\Entity\ChatConnection', $from->resourceId);
        if (!$chatConnection) {
            // create a new ChatConnection instance against the current socket connection
            $chatConnection = new ChatConnection();
            $chatConnection->setId($from->resourceId);
            $em->persist($chatConnection);
            $em->flush();
        }
        $chatConnection
            ->setUser($emitter)
            ->setRoom($chatRoom);
        if (self::DEBUG) echo "\tChatConnection ID : {$chatConnection->getId()}\n";


        // associations
        $chatRoom->addUser($emitter)->addConnection($chatConnection);

        $em->flush();

        $userCount = count($chatRoom->getUsers());
        $now = new \DateTime();

        switch ($message['type'])
        {
            // a client wants to subscribe to the chat room
            case self::MSG_SUBSCRIBE:
                $responseMessage = array(
                    'id'                => md5(round(microtime(true) * 1000) . $message['emitter']['username']),
                    'type'              => self::MSG_JOINED,
                    'room'              => array_merge($message['room'], array('count' => $userCount)),
                    'emitter'           => $message['emitter'],
                    'timestamp_server'  => $now->format('c') // ISO 8601
                );

                $messageToEmitter = $responseMessage;
                $messageToEmitter['type'] = self::MSG_SUBSCRIBE_ACK;

                $this->sendMessage($from, $chatRoom->getId(), $responseMessage, $messageToEmitter);
                break;

            case self::MSG_MESSAGE:

                $messageToUsers = array(
                    'id'                => md5(round(microtime(true) * 1000) . $message['emitter']['username']),
                    'text'              => $message['text'],
                    'type'              => self::MSG_PUBLISH,
                    'room'              => array_merge($message['room'], array('count' => $userCount)),
                    'emitter'           => $message['emitter'],
                    'timestamp_server'  => $now->format('c') // ISO 8601
                );

                $messageToEmitter = array(
                    'id'                => md5(round(microtime(true) * 1000) . $message['emitter']['username']),
                    'correlationID'     => $message['id'],
                    'type'              => self::MSG_PUBLISH_ACK,
                    'room'              => array_merge($message['room'], array('count' => $userCount)),
                    'emitter'           => $message['emitter'],
                    'timestamp_server'  => $now->format('c') // ISO 8601
                );

                echo sprintf('Connection %s (%d) sending message "%s" to %d other connection%s' . "\n"
                    , $from->remoteAddress, $from->resourceId, $message, $userCount, $userCount == 1 ? '' : 's');

                $this->sendMessage($chatConnection, $messageToUsers, $messageToEmitter);

                // create a ChatMessage instance
                $messageToStore = ChatMessage::createNewFrom($message);

                $messageToStore->setEmitter($emitter);
                $messageToStore->setRoom($chatRoom);

                if (self::DEBUG) echo "\t persisting message n°{$messageToStore->getId()} ...\n";
                $em->persist($messageToStore);
                $em->flush();
                if (self::DEBUG) echo "\t... persisting OK\n";
                break;
        }

        if (self::DEBUG) echo "END dispatchMessage <===\n\n";
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        if (self::DEBUG) echo "===> BEGIN onOpen\n";

        // Store the new connection to send messages on later
        $this->clients->attach($conn);

        echo "\tNew connection! ({$conn->resourceId}) on {$conn->remoteAddress}\n";

        // create / retrieve the ChatConnection object
//        $em = $this->service->getEntityManager();
//        try {
//            $chatConnection = $em->find('AppBundle\Entity\ChatConnection', $conn->resourceId);
//            if (!$chatConnection) {
//                // create a new ChatConnection instance against the current socket connection
//                $chatConnection = new ChatConnection();
//                $chatConnection->setId($conn->resourceId);
//                $em->persist($chatConnection);
//                $em->flush();
//            }
//        if (self::DEBUG) echo "\tChatConnection ID : {$chatConnection->getId()}\n";

//        } catch (\Exception $ex) {
//            echo "\tError : {$ex->getTraceAsString()}\n";
//        }


        if (self::DEBUG) echo "END onOpen <===\n\n";
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (self::DEBUG) echo "===> BEGIN onMessage\n";

        $message = json_decode($msg, true);

        if (self::DEBUG) {
            $query = $from->WebSocket->request;
            $id = $message['id'];
            echo "\tRequest of the incoming message n°{$id}:\n\t";
            print_r("query={$query}\n");
        }

        $this->dispatchMessage($from, $message);

        if (self::DEBUG) echo "END onMessage <===\n\n";
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        if (self::DEBUG) echo "===> BEGIN onClose\n";

        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $chatConnection = null;
        try {
            // retrieve the related ChatConnection instance
            $em = $this->service->getEntityManager();
            $chatConnection = $this->service->getEntityManager()->find('AppBundle\Entity\Chatconnection', $conn->resourceId);

            // send a LEFT message to users
            $chatRoom = $chatConnection->getRoom();
            $chatUser = $chatConnection->getUser();
            $now = new \DateTime();
            $message = array(
                'id'        => md5(round(microtime(true) * 1000)),
                'type'      => self::MSG_LEFT,
                'room'      => array(
                    'id'        => $chatRoom->getId(),
                    'count'     => count($chatRoom->getUsers())
                ),
                'emitter'   => array(
                    'username'  => $chatUser->getUsername(),
                    'fullname'  => $chatUser->getFullname(),
                    'address'   => $chatUser->getId()
                ),
                'timestamp_server' => $now->format('c') // ISO 8601
            );
            $this->sendMessage($chatConnection, $message, null);

            // remove the related ChatConnection instance
            if (self::DEBUG) echo "\tremoving the closed connection ...\n";
            $em->remove($chatConnection);
            $em->flush();
            if (self::DEBUG) echo "\t... removing OK\n";
        }
        catch (\Exception $ex) {
            echo "\tError while closing : {$ex->getTraceAsString()}";
        }

        echo "\tConnection {$conn->resourceId} has disconnected\n";

        if (self::DEBUG) echo "END onClose <===\n\n";
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        if (self::DEBUG) echo "===> BEGIN onError\n";

        echo "An error has occurred on line {$e->getLine()} in file {$e->getFile()}:\n\t{$e->getCode()}: {$e->getMessage()}\n";

        $conn->close();
        $this->service->getEntityManager()->getConnection()->close();

        if (self::DEBUG) echo "END onError <===\n\n";
    }
}