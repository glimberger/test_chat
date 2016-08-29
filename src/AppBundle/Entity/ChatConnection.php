<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ratchet\ConnectionInterface;

/**
 * Class Connection
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="chat_connection")
 */
class ChatConnection
{
    /**
     * Connection Resource ID.
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The user using this connection.
     * @var ChatUser
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChatUser", inversedBy="connections")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @var ChatRoom
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ChatRoom" ,inversedBy="connections")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=true)
     */
    protected $room;

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ChatConnection
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * @param \SplObjectStorage $set
     * @return ConnectionInterface
     */
    public function getConnection(\SplObjectStorage $set)
    {
        if (!$this->connection) {
            /** @var ConnectionInterface $conn */
            foreach ($set as $conn) {
                if ($conn->resourceId == $this->id) {
                    $this->connection = $conn;
                    break;
                }
            }
        }
        return $this->connection;
    }

    /**
     * Set user
     *
     * @param ChatUser $user
     *
     * @return ChatConnection
     */
    public function setUser(ChatUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return ChatUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set room
     *
     * @param ChatRoom $room
     *
     * @return ChatConnection
     */
    public function setRoom(ChatRoom $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return ChatRoom
     */
    public function getRoom()
    {
        return $this->room;
    }
}
