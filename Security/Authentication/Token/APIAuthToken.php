<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 01.04.2016 12:45
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class APIAuthToken extends AbstractToken
{
    protected $rawToken;

    /**
     * {@inheritdoc}
     */
    public function __construct($user, array $roles = [])
    {
        parent::__construct($roles);
        
        $this->setAuthenticated(count($roles) > 0);
        $this->setUser($user);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return $this->rawToken;
    }

    /**
     * @return mixed
     */
    public function getRawToken()
    {
        return $this->rawToken;
    }

    /**
     * @param mixed $rawToken
     * @return APIAuthToken
     */
    public function setRawToken($rawToken)
    {
        $this->rawToken = $rawToken;
        return $this;
    }

}