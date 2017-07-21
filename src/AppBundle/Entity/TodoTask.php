<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoTaskRepository")
 * @ORM\Table(name="todo_task",
 *             indexes={@ORM\Index(name="todo_task_idx", columns={"id"})}
 * )
 */
class TodoTask
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="description", type="string", length=256, nullable=false)
     */
    protected $description;
    /**
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="tasks")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $list;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @ORM\Column(name="is_completed", type="boolean", nullable=true)
     */
    protected $isCompleted;
    /**
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    protected $createdOn;

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
     * Set description
     *
     * @param string $description
     *
     * @return TodoTask
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return TodoTask
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
     * Set list
     *
     * @param \AppBundle\Entity\TodoList $list
     *
     * @return TodoTask
     */
    public function setList(\AppBundle\Entity\TodoList $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return \AppBundle\Entity\TodoList
     */
    public function getList()
    {
        return $this->list;
    }


    /**
     * Set isCompleted
     *
     * @param boolean $isCompleted
     *
     * @return TodoTask
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * Get isCompleted
     *
     * @return boolean
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return TodoTask
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }
}
