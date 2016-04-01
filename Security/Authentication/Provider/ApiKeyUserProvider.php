<?php
/**
 * Project by cp.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 16:45
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Provider;

use CoreSite\APIAuthBundle\Service\TokenManager;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use FOS\UserBundle\Doctrine\UserManager;

class ApiKeyUserProvider implements UserProviderInterface
{
    
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var TokenManager
     */
    private $tokenManager;

    public function __construct(UserManager $userManager, TokenManager $tokenManager)
    {
        $this->userManager = $userManager;
        $this->tokenManager = $tokenManager;
    }

    public function getUsernameForApiKey($apiKey)
    {
        $user = $this->tokenManager->getUserByToken($apiKey);

        if(!$user instanceof UserInterface)
        {
            return false;
        }
        
        return $user->getUsername();
    }


    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if(!$user instanceof UserInterface)
        {
            throw new UsernameNotFoundException(sprintf('No record found for user %s', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}