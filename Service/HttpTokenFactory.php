<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Service;


use CoreSite\APIAuthBundle\Entity\HttpToken;
use FOS\UserBundle\Model\UserInterface;

class HttpTokenFactory
{
    const LIFE_TIME_MAX         = 86400;
    const LIFE_TIME_REFRESH     = 86400;

    private $tokenManager;

    public function __construct(HttpTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function createToken(UserInterface $user) : HttpToken
    {
        return $this->tokenManager->createToken($user);
    }
}