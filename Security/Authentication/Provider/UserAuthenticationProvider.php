<?php
/**
 * Project sf3.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 17.01.2017 15:26
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Provider;


use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserAuthenticationProvider implements AuthenticationProviderInterface
{

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        // TODO: Implement supports() method.
    }

    public function authenticate(TokenInterface $token)
    {
        // TODO: Implement authenticate() method.
    }
}