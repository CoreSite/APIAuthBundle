<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * Данные об успешно аутифицированных пользователях
 *
 * @ORM\Table(name="cs_http_token")
 * @ORM\Entity(repositoryClass="CoreSite\APIAuthBundle\Entity\Repository\TokenHttpRepository")
 */
class HttpToken
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="refresh_to", type="datetime")
     */
    private $refreshTo;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private $expiresAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recovery", type="boolean", options={"default" = false})
     */
    private $recovery;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return HttpToken
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return HttpToken
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return HttpToken
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set refreshTo
     *
     * @param \DateTime $refreshTo
     *
     * @return HttpToken
     */
    public function setRefreshTo($refreshTo)
    {
        $this->refreshTo = $refreshTo;

        return $this;
    }

    /**
     * Get refreshTo
     *
     * @return \DateTime
     */
    public function getRefreshTo()
    {
        return $this->refreshTo;
    }



    /**
     * Set recovery
     *
     * @param boolean $recovery
     *
     * @return HttpToken
     */
    public function setRecovery($recovery)
    {
        $this->recovery = $recovery;

        return $this;
    }

    /**
     * Get recovery
     *
     * @return boolean
     */
    public function getRecovery()
    {
        return $this->recovery;
    }
}
