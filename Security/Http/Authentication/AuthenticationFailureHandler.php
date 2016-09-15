<?php
/**
 * Project by cp.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 18:08
 */

namespace CoreSite\APIAuthBundle\Security\Http\Authentication;

use CoreSite\APIAuthBundle\Event\Event;
use CoreSite\APIAuthBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Class AuthenticationFailureHandler
 *
 * Перехват ошибок аутификации и форматирование ответа в JSON.
 *
 * @package CoreSite\APIAuthBundle\Security\Http\Authentication
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    const RESPONSE_CODE    = 401;
    const RESPONSE_MESSAGE = 'cs_auth_api.bad_username_or_password';

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
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
        $data = [
            'code'    => self::RESPONSE_CODE,
            'message' => self::RESPONSE_MESSAGE,
        ];

        $response = new JsonResponse($data, self::RESPONSE_CODE);
        $event = new AuthenticationFailureEvent($request, $exception, $response);

        $this->dispatcher->dispatch(Event::AUTHENTICATION_FAILURE, $event);

        return $event->getResponse();
    }
}