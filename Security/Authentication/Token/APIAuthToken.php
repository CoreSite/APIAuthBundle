<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Security\Authentication\Token;


use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class APIAuthToken
 * 
 * Токен пользователя после успешной аутификации, используется для авторизации в системе
 * 
 * @package CoreSite\APIAuthBundle\Security\Authentication\Token
 */
class APIAuthToken extends AbstractToken
{
    private $credentials;
    private $providerKey;

    /**
     * Устанавливается для постоянных токенов
     *
     * @var bool
     */
    private $constant;

    /**
     * APIAuthToken constructor.
     * @param array|\string[]|\Symfony\Component\Security\Core\Role\RoleInterface[] $user
     * @param $credentials
     * @param $providerKey
     * @param array $roles
     * @param bool $constant
     */
    public function __construct($user, $credentials, $providerKey, array $roles = [], $constant = false)
    {
        parent::__construct($roles);

        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->setUser($user);
        $this->credentials = $credentials;
        $this->providerKey = $providerKey;
        $this->constant = $constant;

        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthenticated($isAuthenticated)
    {
        if ($isAuthenticated) {
            throw new \LogicException('Cannot set this token to trusted after instantiation.');
        }

        parent::setAuthenticated(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Returns the provider key.
     *
     * @return string The provider key
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }
    
    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->credentials, $this->providerKey, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->credentials, $this->providerKey, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }

    /**
     * @return boolean
     */
    public function isConstant(): bool
    {
        return (bool)$this->constant;
    }

    /**
     * @param boolean $constant
     * @return APIAuthToken
     */
    public function setConstant(bool $constant): APIAuthToken
    {
        $this->constant = $constant;
        return $this;
    }

}