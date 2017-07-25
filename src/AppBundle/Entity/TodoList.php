<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoListRepository")
 * @ORM\Table(name="todo_list",
 *             indexes={@ORM\Index(name="todo_list_idx", columns={"id"})}
 * )
 */
class TodoList
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    protected $name;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @ORM\OneToMany(targetEntity="TodoTask", mappedBy="list")
     */
    protected $tasks;
    /**
     * @ORM\Column(name="is_archived", type="boolean", nullable=true)
     */
    protected $isArchived;
    /**
     * @ORM\Column(name="is_deletion_pending", type="boolean", nullable=true)
     */
    protected $isDeletionPending;


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
     * Set name
     *
     * @param string $name
     *
     * @return TodoList
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return TodoList
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\TodoTask $task
     *
     * @return TodoList
     */
    public function addTask(\AppBundle\Entity\TodoTask $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \AppBundle\Entity\TodoTask $task
     */
    public function removeTask(\AppBundle\Entity\TodoTask $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }


    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     *
     * @return TodoList
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set isDeletionPending
     *
     * @param boolean $isDeletionPending
     *
     * @return TodoList
     */
    public function setIsDeletionPending($isDeletionPending)
    {
        $this->isDeletionPending = $isDeletionPending;

        return $this;
    }

    /**
     * Get isDeletionPending
     *
     * @return boolean
     */
    public function getIsDeletionPending()
    {
        return $this->isDeletionPending;
    }
}
