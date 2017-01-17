<?php
/**
 * Project sf3.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 17.01.2017 15:26
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Provider;


use CoreSite\APIAuthBundle\Security\Authentication\Token\APIAuthToken;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class ApiUserAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var string
     */
    private $providerKey;

    public function __construct(UserCheckerInterface $userChecker, $providerKey, $hideUserNotFoundExceptions, UserManager $userManager)
    {
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
        $this->userManager = $userManager;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof APIAuthToken;
    }


    /**
     * Retrieves the user from an implementation-specific location.
     *
     * @param string $username The username to retrieve
     * @param TokenInterface $token The Token
     *
     * @return UserInterface The user
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    protected function retrieveUser($username, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }

        try {
            $user = $this->userManager->findUserByUsername($username);

            if (!$user instanceof UserInterface) {
                throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
            }

            return $user;
        }
        catch (UsernameNotFoundException $notFound) {
            throw $notFound;
        }
        catch (\Exception $repositoryProblem) {
            throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, 0, $repositoryProblem);
        }
    }

    /**
     * Does additional checks on the user and token (like validating the
     * credentials).
     *
     * @param UserInterface $user The retrieved UserInterface instance
     * @param TokenInterface $token The UsernamePasswordToken token to be authenticated
     *
     * @throws AuthenticationException if the credentials could not be validated
     */
    protected function checkAuthentication(UserInterface $user, TokenInterface $token)
    {
        $currentUser = $token->getUser();
        if(!$currentUser instanceof UserInterface) {

        } else {

        }
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $username = $token->getUsername();
        if (empty($username)) {
            $username = 'NONE_PROVIDED';
        }

        try {
            $user = $this->retrieveUser($username, $token);
        } catch (UsernameNotFoundException $notFound) {
            $notFound->setUsername($username);

            throw $notFound;
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
        }

        try {
            $this->userChecker->checkPreAuth($user);
            $this->checkAuthentication($user, $token);
            $this->userChecker->checkPostAuth($user);
        } catch (BadCredentialsException $e) {
            throw $e;
        }

        $authenticatedToken = new APIAuthToken($user, $token->getCredentials(), $this->providerKey, $this->getRoles($user, $token));
        $authenticatedToken->setAttributes($token->getAttributes());

        return $authenticatedToken;
    }

    /**
     * Retrieves roles from user and appends SwitchUserRole if original token contained one.
     *
     * @param UserInterface $user The user
     * @param TokenInterface $token The token
     *
     * @return array The user roles
     */
    private function getRoles(UserInterface $user, TokenInterface $token)
    {
        $roles = $user->getRoles();

        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $roles[] = $role;

                break;
            }
        }

        return $roles;
    }
}