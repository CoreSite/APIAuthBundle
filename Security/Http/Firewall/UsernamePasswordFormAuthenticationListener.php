<?php

namespace CoreSite\APIAuthBundle\Security\Http\Firewall;

use CoreSite\APIAuthBundle\Security\Authentication\Token\APIAuthToken;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class UsernamePasswordFormAuthenticationListener
 * @package CoreSite\APIAuthBundle\Security\Http\Firewall
 */
class UsernamePasswordFormAuthenticationListener extends AbstractAuthenticationListener
{
    //private $csrfTokenManager;

    //protected $providerKey;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        //var_dump($providerKey);
        parent::__construct($tokenStorage, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
            'username_parameter' => '_username',
            'password_parameter' => '_password',
//            'csrf_parameter' => '_csrf_token',
//            'csrf_token_id' => 'authenticate',
            'post_only' => true,
        ), $options), $logger, $dispatcher);

        //$this->csrfTokenManager = $csrfTokenManager;

    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if ($this->options['post_only'] && !$request->isMethod('POST')) {
            return false;
        }

        return parent::requiresAuthentication($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        //print '111';

//        if (null !== $this->csrfTokenManager) {
//            $csrfToken = ParameterBagUtils::getRequestParameterValue($request, $this->options['csrf_parameter']);
//
//            if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken($this->options['csrf_token_id'], $csrfToken))) {
//                throw new InvalidCsrfTokenException('Invalid CSRF token.');
//            }
//        }

        if ($this->options['post_only']) {
            $username = trim($request->get($this->options['username_parameter'], null));
            $password = $request->get($this->options['password_parameter'], null);
        } else {
            $username = trim($request->get($this->options['username_parameter'], null));
            $password = $request->get($this->options['password_parameter'], null);
        }

        //$request->getSession()->set(Security::LAST_USERNAME, $username);

        print '222';

        //var_dump($username); var_dump($password);

        $tokenOrResponse = $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));

        print '333';
        var_dump(get_class($tokenOrResponse));

        return $tokenOrResponse; //$this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }
}
