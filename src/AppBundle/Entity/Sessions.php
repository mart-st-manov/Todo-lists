<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionsRepository")
 * @ORM\Table(name="sessions",
 *             indexes={@ORM\Index(name="session_idx", columns={"sess_id"})}
 * )
 */
class Sessions

{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="sess_id", length=128)
     */
    protected $sessId;

    /**
     * @ORM\Column(type="blob", name="sess_data")
     */
    protected $sessData;

    /**
     * @ORM\Column(type="integer", name="sess_time", options={"unsigned"=true})
     */
    protected $sessTime;

    /**
     * @ORM\Column(type="integer", name="sess_lifetime", options={"unsigned"=true})
     */
    protected $sessLifetime;

    /**
     * Set sessId
     *
     * @param string $sessId
     *
     * @return Sessions
     */
    public function setSessId($sessId)
    {
        $this->sessId = $sessId;

        return $this;
    }

    /**
     * Get sessId
     *
     * @return string
     */
    public function getSessId()
    {
        return $this->sessId;
    }

    /**
     * Set sessData
     *
     * @param string $sessData
     *
     * @return Sessions
     */
    public function setSessData($sessData)
    {
        $this->sessData = $sessData;

        return $this;
    }

    /**
     * Get sessData
     *
     * @return string
     */
    public function getSessData()
    {
        return $this->sessData;
    }

    /**
     * Set sessTime
     *
     * @param integer $sessTime
     *
     * @return Sessions
     */
    public function setSessTime($sessTime)
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    /**
     * Get sessTime
     *
     * @return integer
     */
    public function getSessTime()
    {
        return $this->sessTime;
    }

    /**
     * Set sessLifetime
     *
     * @param integer $sessLifetime
     *
     * @return Sessions
     */
    public function setSessLifetime($sessLifetime)
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }

    /**
     * Get sessLifetime
     *
     * @return integer
     */
    public function getSessLifetime()
    {
        return $this->sessLifetime;
    }
}
