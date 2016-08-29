<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Room
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="chat_room")
 */
class ChatRoom
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChatMessage", mappedBy="room")
     */
    protected $messages;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ChatConnection", mappedBy="room")
     */
    protected $connections;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ChatUser", mappedBy="rooms")
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->connections = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return ChatRoom
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add message
     *
     * @param ChatMessage $message
     *
     * @return ChatRoom
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

    /**
     * Add connection
     *
     * @param ChatConnection $connection
     *
     * @return ChatRoom
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
     * Add user
     *
     * @param ChatUser $user
     *
     * @return ChatRoom
     */
    public function addUser(ChatUser $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param ChatUser $user
     */
    public function removeUser(ChatUser $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
