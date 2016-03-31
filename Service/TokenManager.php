<?php

declare(strict_types=1);

/**
 * Project by CP.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 19:10
 */

namespace CoreSite\APIAuthBundle\Service;


use CoreSite\APIAuthBundle\Entity\Token;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Security\Core\User\UserInterface;


class TokenManager
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(UserManager $userManager, EntityManager $entityManager)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

    public function getUserByToken(string $token)
    {
        $token = $this->entityManager->getRepository('CoreSiteAPIAuthBundle:Token')->getToken($token);
        if(!$token instanceof Token)
        {
            return false;
        }

        $user = $this->userManager->findUserBy(array('id' => $token->getUserId()));
        if(!$user instanceof UserInterface)
        {
            return false;
        }

        return $user;
    }

}