<?php
/**
 * Project by cp.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 01.04.2016 12:30
 */

namespace CoreSite\APIAuthBundle\Security\Firewall;


use CoreSite\APIAuthBundle\Security\Authentication\Token\APIAuthToken;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class APIAuthListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $tokenExtractors;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface                 $authenticationManager
     * @param array                                          $config
     */
    public function __construct($tokenStorage, AuthenticationManagerInterface $authenticationManager, array $config = [])
    {
        if (!$tokenStorage instanceof TokenStorageInterface) {
            throw new \InvalidArgumentException('Argument 1 should be an instance of Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $this->tokenStorage          = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->config                = array_merge(['throw_exceptions' => false], $config);
        $this->tokenExtractors       = [];
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
//        if (!($requestToken = $this->getRequestToken($event->getRequest()))) {
//            return;
//        }
//
//        $token = new APIAuthToken();
//        $token->setRawToken($requestToken);

        $request = $event->getRequest();

        //var_dump(123);

        $token = new APIAuthToken();
        $token
            ->setRawToken('1234567890')
        ;

        //var_dump(123);

        try {

            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);

            return;

        } catch (AuthenticationException $failed) {
            if ($this->config['throw_exceptions']) {
                throw $failed;
            }

            $response = new Response();
            $response->setStatusCode(401);
            $response->headers->set('WWW-Authenticate', 'Bearer');
            $event->setResponse($response);
        }
    }
}