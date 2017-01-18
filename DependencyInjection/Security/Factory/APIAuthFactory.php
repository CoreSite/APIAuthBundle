<?php
/**
 * Project by CoreSite APIAuth.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 01.04.2016 16:32
 */

namespace CoreSite\APIAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

/**
 * Class APIAuthFactory
 *
 * Регистрация в системе нового типа аутификации
 *
 * @package CoreSite\APIAuthBundle\DependencyInjection\Security\Factory
 */
class APIAuthFactory extends FormLoginFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'cs_apiauth.authentication.provider.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('cs_apiauth.authentication.provider'))
            ->replaceArgument(1, $id)
        ;

        return $providerId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = 'cs_apiauth_authentication_listener.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('cs_apiauth_authentication_listener'));

        return $listenerId;
    }

    public function getKey()
    {
        return 'cs_apiauth_login';
    }
}