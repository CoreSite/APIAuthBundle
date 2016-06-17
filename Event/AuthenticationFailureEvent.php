<?php
/**
 * Project by CoreSite APIAuth.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthenticationFailureEvent
 *
 * Объект события ошибок аутификации.
 *
 * @package CoreSite\APIAuthBundle\Event
 */
class AuthenticationFailureEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AuthenticationException
     */
    protected $exception;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     * @param Response                $response
     */
    public function __construct(Request $request, AuthenticationException $exception, Response $response)
    {
        $this->request = $request;
        $this->exception = $exception;
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return AuthenticationException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
