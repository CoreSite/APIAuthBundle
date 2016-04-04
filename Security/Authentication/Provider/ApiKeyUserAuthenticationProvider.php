<?php
/**
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * Date: 29.01.2015
 * Time: 16:58
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Provider;

use CoreSite\APIAuthBundle\Security\Authentication\Token\APIAuthToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//use FC\UserBundle\Service\Service;

/**
 * Провайдер аутификации
 *
 * Class ApiKeyUserAuthenticationProvider
 * @package CoreSite\APIAuthBundle\Security\Authentication\Provider
 */
class ApiKeyUserAuthenticationProvider implements AuthenticationProviderInterface
{

	private $hideUserNotFoundExceptions;
	private $userChecker;
	private $providerKey;

	private $encoderFactory;
	private $userProvider;

	/**
	 * @param
	 * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
	 * @param UserCheckerInterface $userChecker
	 * @param $providerKey
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param bool $hideUserNotFoundExceptions
	 */
	public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory)
	{
		//var_dump('ApiKeyUserAuthenticationProvider');
		if (empty($providerKey))
		{
			throw new \InvalidArgumentException('$providerKey must not be empty.');
		}

		//var_dump(get_class($userProvider));

		$this->userChecker = $userChecker;
		$this->providerKey = $providerKey;

		$this->encoderFactory   = $encoderFactory;
		$this->userProvider     = $userProvider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(TokenInterface $token)
	{
		if (!$this->supports($token))
		{
			return false;
		}

		$username = $token->getUsername();
		if (empty($username))
		{
			$username = 'NONE_PROVIDED';
		}

		try
		{
			$user = $this->retrieveUser($username, $token);
		}
		catch (UsernameNotFoundException $notFound)
		{
			if ($this->hideUserNotFoundExceptions)
			{
				throw new BadCredentialsException('Username not found.', 0, $notFound);
			}
			$notFound->setUsername($username);

			throw $notFound;
		}

		if (!$user instanceof UserInterface)
		{
			throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
		}

		try
		{
			$this->userChecker->checkPreAuth($user);
			$this->checkAuthentication($user, $token);
			$this->userChecker->checkPostAuth($user);
		}
		catch (BadCredentialsException $e)
		{
			if ($this->hideUserNotFoundExceptions)
			{
				throw new BadCredentialsException('fc.user.login_page.invalid_code', 0, $e);
			}

			throw $e;
		}

		//$authenticatedToken = new UsernamePasswordCodeToken($user, $token->getCredentials(), $token->getCode(), $this->providerKey, $this->getRoles($user, $token));
		//$authenticatedToken->setAttributes($token->getAttributes());

		$authenticatedToken = new APIAuthToken($username);
		$authenticatedToken
			->setAttributes($token->getAttributes())
		;

		return $authenticatedToken;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function checkAuthentication(UserInterface $user, APIAuthToken $token)
	{
		//var_dump('checkAuthentication');
		$currentUser = $token->getUser();
		if ($currentUser instanceof UserInterface) {
			if ($currentUser->getPassword() !== $user->getPassword()) {
				throw new BadCredentialsException('The credentials were changed from another session.');
			}
		} else {
			if ("" === ($presentedPassword = $token->getCredentials())) {
				throw new BadCredentialsException('The presented password cannot be empty.');
			}

			if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $presentedPassword, $user->getSalt())) {
				throw new BadCredentialsException('The presented password is invalid.');
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function retrieveUser($username, APIAuthToken $token)
	{
		$user = $token->getUser();
		if ($user instanceof UserInterface)
		{
			return $user;
		}

		try
		{
			$user = $this->userProvider->loadUserByUsername($username);

			if (!$user instanceof UserInterface)
			{
				throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
			}

			return $user;
		}
		catch (UsernameNotFoundException $notFound)
		{
			throw $notFound;
		}
		catch (\Exception $repositoryProblem)
		{
			throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, 0, $repositoryProblem);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function supports(TokenInterface $token)
	{
		return $token instanceof APIAuthToken;
	}

	/**
	 * Retrieves roles from user and appends SwitchUserRole if original token contained one.
	 *
	 * @param UserInterface  $user  The user
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