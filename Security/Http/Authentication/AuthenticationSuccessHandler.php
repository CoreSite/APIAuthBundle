<?php
/**
 * Project by cp.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 18:07
 */

namespace CoreSite\APIAuthBundle\Security\Http\Authentication;

use CoreSite\APIAuthBundle\Event\AuthenticationSuccessEvent;
use CoreSite\APIAuthBundle\Service\HttpTokenFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use CoreSite\APIAuthBundle\Event\Event;

/**
 * Class AuthenticationSuccessHandler
 *
 * Перехват успешной аутификации и форматирование ответа в JSON.
 *
 * @package CoreSite\APIAuthBundle\Security\Http\Authentication
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    const RESPONSE_CODE    = 200;
    const RESPONSE_MESSAGE = 'Success.';

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    protected $httpTokenFactory;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param HttpTokenFactory $httpTokenFactory
     */
    public function __construct(EventDispatcherInterface $dispatcher, HttpTokenFactory $httpTokenFactory)
    {
        $this->dispatcher = $dispatcher;
        $this->httpTokenFactory = $httpTokenFactory;
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
        $user = $token->getUser();

        $httpToken = $this->httpTokenFactory->createToken($user);

        // Сохраняем токен в сессии <<
        $request->cookies->set(HttpTokenFactory::SESSION_NAME, $httpToken->getId());
        //$request->getSession()->set(HttpTokenFactory::SESSION_NAME, $httpToken->getId());
        // Сохраняем токен в сессии >>

        $response = new JsonResponse();
        $event    = new AuthenticationSuccessEvent([
            'token'         => $httpToken->getId(),
            'expires'       => $httpToken->getExpiresAt()->getTimestamp(),
            'refresh_to'    => $httpToken->getExpiresAt()->getTimestamp(),
            'code'          => self::RESPONSE_CODE,
            'message'       => self::RESPONSE_MESSAGE,
        ], $user, $request, $response);

        $this->dispatcher->dispatch(Event::AUTHENTICATION_SUCCESS, $event);
        $response->setData($event->getData());

        return $response;
    }
}