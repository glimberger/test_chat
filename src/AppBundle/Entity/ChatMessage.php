<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="chat_message")
 */
class ChatMessage
{
    const SUBSCRIBE     = "subscribe";
    const SUBSCRIBE_ACK = 'subscribe_acknowledge';
    const PUBLISH       = 'publish';
    const PUBLISH_ACK   = 'publish_acknowledge';
    const JOINED        = 'joined';
    const LEFT          = 'left';
    const BEGIN_TYP     = 'begin_typing';
    const END_TYP       = 'end_typing';
    const MESSAGE       = 'message';

    /**
     * Message unique identifier
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    protected $id;

    /**
     * Correlation ID provided by a <..>_ACK message.
     * @var string
     */
    protected $correlationId;

    /**
     * Text of the message.
     * @var string
     *
     * @ORM\Column(name="text", type="string", nullable=true)
     */
    protected $text;

    /**
     * Message type.
     *
     * @var string
     */
    protected $type;

    /**
     * Creation date.
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * Publish date.
     * @var \DateTime
     *
     * @ORM\Column(name="date_published", type="datetime", nullable=true)
     */
    protected $datePublished;

    /**
     * The chat room where to publish.
     * @var ChatRoom
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChatRoom", inversedBy="messages")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * Message emitter.
     * @var ChatUser
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChatUser", inversedBy="")
     * @ORM\JoinColumn(name="emitter_id", referencedColumnName="id")
     */
    protected $emitter;

    /**
     * @param array $msg
     * @return ChatMessage
     */
    public static function createNewFrom(array $msg)
    {
        $message = new ChatMessage();
        $message
            ->setId($msg['id'])
            ->setType($msg['type'])
            ->setDateCreated(new \DateTime($msg['timestamp_emitter']));
        if (isset($msg['text'])) {
            $message->setText($msg['text']);
        }

        return $message;
    }

    /**
     * @param ChatMessage $msg
     */
    public static function toArray(ChatMessage $msg)
    {
        $message = array(
            'id'                => md5(round(microtime(true) * 1000) . $msg->getEmitter()->getUsername()),
            'type'              => $msg->getType(),
            'room'              => array(
                'roomID'            => $msg->getRoom()->getId(),
                'count'             => count($msg->getRoom()->getUsers())
            ),
            'emitter'           => array(
                'username'          => $msg->getEmitter()->getUsername(),
                'fullname'          => $msg->getEmitter()->getFullname(),
                'address'           => $msg->getId()
            ),
            // ISO 8601 format
            'timestamp_emitter' => $msg->getDateCreated()->format('c'),
        );

        // options
        if ($msg->getText()) {
            $message['text'] = $msg->getText();
        }
        if ($msg->getDatePublished()) {
            // ISO 8601 format
            $message['timestamp_server'] = $msg->getDatePublished()->format('c');
        }
        if ($msg->getCorrelationId()) {
            $message['correlationID'] = $msg->getCorrelationId();
        }
    }

    /**
     * ChatMessage constructor.
     */
    public function __construct()
    {
        $this->text = null;
        $this->datePublished = null;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ChatMessage
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get correlation ID
     *
     * @return string
     */
    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    /**
     * Set correlation ID
     *
     * @param string $correlationId
     * @return ChatMessage
     */
    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return ChatMessage
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     * @return ChatMessage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set room
     *
     * @param \AppBundle\Entity\ChatRoom $room
     *
     * @return ChatMessage
     */
    public function setRoom(ChatRoom $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \AppBundle\Entity\ChatRoom
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set emitter
     *
     * @param \AppBundle\Entity\ChatUser $emitter
     *
     * @return ChatMessage
     */
    public function setEmitter(ChatUser $emitter = null)
    {
        $this->emitter = $emitter;

        return $this;
    }

    /**
     * Get emitter
     *
     * @return \AppBundle\Entity\ChatUser
     */
    public function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return ChatMessage
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set datePublished
     *
     * @param \DateTime $datePublished
     *
     * @return ChatMessage
     */
    public function setDatePublished($datePublished)
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    /**
     * Get datePublished
     *
     * @return \DateTime
     */
    public function getDatePublished()
    {
        return $this->datePublished;
    }
}
