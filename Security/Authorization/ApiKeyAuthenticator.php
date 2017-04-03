<?php
/**
 * Project by CoreSite APIAuth.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 14:57
 */

namespace CoreSite\APIAuthBundle\Security\Authorization;

use CoreSite\APIAuthBundle\Entity\HttpToken;
use CoreSite\APIAuthBundle\Security\Authentication\Provider\ApiKeyUserProvider;
use CoreSite\APIAuthBundle\Security\Authentication\Token\APIAuthToken;
use CoreSite\APIAuthBundle\Service\HttpTokenManager;
use CoreSite\CoreBundle\Entity\AccountInterface;
use CoreSite\CoreBundle\Entity\AccountUserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * Class ApiKeyAuthenticator
 * 
 * Авторизация пользователя по токену
 * 
 * @package CoreSite\APIAuthBundle\Security\Authorization
 */
class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface, AuthenticationSuccessHandlerInterface
{
    const RESPONSE_FAILURE_CODE = 401;

    private $tokenManager;

    public function __construct(HttpTokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function createToken(Request $request, $providerKey)
    {
        $roles = [];
        $constant = false;
        $apiKey = null;
        $authHeader = explode(' ', $request->headers->get('Authorization'));
        if(isset($authHeader[0]) && $authHeader[0] == 'Bearer' && isset($authHeader[1]))
        {
            $apiKey = $authHeader[1];
        }
        elseif(isset($authHeader[0]) && $authHeader[0] == 'Token' && isset($authHeader[1]))
        {
            $apiKey = $authHeader[1];
            $constant = true;
            $roles = ['ROLE_TOKEN_API'];
        }

        if (!$apiKey) {
            return null;
            //throw new BadCredentialsException('No API key found');
        }

        return new APIAuthToken(
            'anon.',
            $apiKey,
            $providerKey,
            $roles,
            $constant
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        /** @var APIAuthToken $token */
        $apiKey = $token->getCredentials();
        $user = null;

        if($token->isConstant())
        {
            $user = $userProvider->loadUserByToken($apiKey);
            if(!$user instanceof UserInterface)
            {
                throw new CustomUserMessageAuthenticationException(
                    sprintf('Token "%s" does not exist.', $apiKey)
                );
            }
        }
        else
        {
            $user = $userProvider->loadUserByApiKey($apiKey);
            if(!$user instanceof UserInterface)
            {
                throw new CustomUserMessageAuthenticationException(
                    sprintf('Bearer "%s" does not exist.', $apiKey)
                );
            }
        }

        if($user instanceof AccountUserInterface && $user->getAccount() instanceof AccountInterface && $user->getAccount()->getEnabled() == false) {
            throw new CustomUserMessageAuthenticationException(sprintf('Account "%s" has been blocked', $user->getAccount()->getTitle()));
        }

        return new APIAuthToken(
            $user,
            $apiKey,
            $providerKey,
            array_merge($user->getRoles(), $token->getRoles()),
            $token->isConstant()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof APIAuthToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'code'      => self::RESPONSE_FAILURE_CODE,
            'message'   => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, self::RESPONSE_FAILURE_CODE);
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $httpToken = $this->tokenManager->getToken($token->getCredentials());
        if($httpToken instanceof HttpToken)
        {
            $this->tokenManager->refreshToken($httpToken);
        }
    }
}