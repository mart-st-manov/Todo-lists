<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="todo_user",
 *             indexes={@ORM\Index(name="user_idx", columns={"id"})}
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;
    /**
     * @ORM\Column(name="username", type="string", length=64, nullable=true, unique=true)
     */
    protected $username;
    /**
     * @ORM\Column(name="plain_password", type="string", length=128, nullable=false)
     */
    protected $plainPassword;
    /**
     * @ORM\Column(name="password", type="string", length=256, nullable=false)
     */
    protected $password;
    /**
     * @ORM\OneToMany(targetEntity="TodoList", mappedBy="user")
     */
    protected $lists;
    /**
     * @ORM\OneToMany(targetEntity="TodoList", mappedBy="user")
     */
    protected $tasks;


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
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add list
     *
     * @param \AppBundle\Entity\TodoList $list
     *
     * @return User
     */
    public function addList(\AppBundle\Entity\TodoList $list)
    {
        $this->lists[] = $list;

        return $this;
    }

    /**
     * Remove list
     *
     * @param \AppBundle\Entity\TodoList $list
     */
    public function removeList(\AppBundle\Entity\TodoList $list)
    {
        $this->lists->removeElement($list);
    }

    /**
     * Get lists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLists()
    {
        return $this->lists;
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\TodoList $task
     *
     * @return User
     */
    public function addTask(\AppBundle\Entity\TodoList $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \AppBundle\Entity\TodoList $task
     */
    public function removeTask(\AppBundle\Entity\TodoList $task)
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
     * Set username
     *
     * @param string $username
     *
     * @return User
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
     * Set plainPassword
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }
    /**
     * @return null
     */
    public function getRoles()
    {
        return null;
    }
    /**
     * @return null
     */
    public function eraseCredentials()
    {
        return null;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
