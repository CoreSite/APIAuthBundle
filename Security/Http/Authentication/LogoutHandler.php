<?php
/**
 * Project cp.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 05.04.2016 17:50
 */

namespace CoreSite\APIAuthBundle\Security\Http\Authentication;


use CoreSite\APIAuthBundle\Entity\HttpToken;
use CoreSite\APIAuthBundle\Service\HttpTokenFactory;
use CoreSite\APIAuthBundle\Service\HttpTokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutHandler
 *
 * Перехват события выхода из системы
 *
 * @package CoreSite\APIAuthBundle\Security\Http\Authentication
 */
class LogoutHandler implements AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{

    const RESPONSE_SUCCESS_CODE    = 200;
    const RESPONSE_FAILURE_CODE    = 401;
    const RESPONSE_SUCCESS_MESSAGE = 'Logout success.';
    const RESPONSE_FAILURE_MESSAGE = 'Logout failure.';

    private $httpTokenManager;

    public function __construct(HttpTokenManager $httpTokenManager)
    {
        $this->httpTokenManager = $httpTokenManager;
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
        // TODO: Implement onAuthenticationFailure() method.
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        $apiKey = null;
        $authHeader = explode(' ', $request->headers->get('Authorization'));
        if(isset($authHeader[0]) && $authHeader[0] == 'Bearer' && isset($authHeader[1]))
        {
            $apiKey = $authHeader[1];
        }

        $httpToken = null;
        if(!$apiKey || !($httpToken = $this->httpTokenManager->getToken($apiKey)) instanceof HttpToken || !$this->httpTokenManager->deleteToken($httpToken))
        {
            $data = [
                'code'    => self::RESPONSE_FAILURE_CODE,
                'message' => self::RESPONSE_FAILURE_MESSAGE,
            ];

            return new JsonResponse($data, self::RESPONSE_FAILURE_CODE);
        }

        // Удаляем токен сессии <<
        $request->cookies->remove(HttpTokenFactory::SESSION_NAME);
        //$request->getSession()->remove(HttpTokenFactory::SESSION_NAME);
        // Удаляем токен сессии >>

        $data = [
            'code'    => self::RESPONSE_SUCCESS_CODE,
            'message' => self::RESPONSE_SUCCESS_MESSAGE,
        ];

        return new JsonResponse($data, self::RESPONSE_SUCCESS_CODE);
    }
}