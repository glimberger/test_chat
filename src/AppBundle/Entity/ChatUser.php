<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class RoomUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="chat_user")
 */
class ChatUser
{
    /**
     * User's machine IP address.
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string")
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string")
     */
    protected $fullname;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChatConnection", mappedBy="user")
     */
    protected $connections;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ChatRoom", inversedBy="users")
     * @ORM\JoinTable(name="users_rooms",
     *     joinColumns={@ORM\JoinColumn(name="chatuser_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="chatroom_id", referencedColumnName="id")})
     */
    protected $rooms;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChatMessage", mappedBy="emitter")
     */
    protected $messages;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->connections = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ChatUser
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
     * Set username
     *
     * @param string $username
     *
     * @return ChatUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return ChatUser
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Add connection
     *
     * @param ChatConnection $connection
     *
     * @return ChatUser
     */
    public function addConnection(ChatConnection $connection)
    {
        $this->connections[] = $connection;

        return $this;
    }

    /**
     * Remove connection
     *
     * @param ChatConnection $connection
     */
    public function removeConnection(ChatConnection $connection)
    {
        $this->connections->removeElement($connection);
    }

    /**
     * Get connections
     *
     * @return Collection
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Add room
     *
     * @param ChatRoom $room
     *
     * @return ChatUser
     */
    public function addRoom(ChatRoom $room)
    {
        $this->rooms[] = $room;

        return $this;
    }

    /**
     * Remove room
     *
     * @param ChatRoom $room
     */
    public function removeRoom(ChatRoom $room)
    {
        $this->rooms->removeElement($room);
    }

    /**
     * Get rooms
     *
     * @return Collection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Add message
     *
     * @param ChatMessage $message
     *
     * @return ChatUser
     */
    public function addMessage(ChatMessage $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param ChatMessage $message
     */
    public function removeMessage(ChatMessage $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
