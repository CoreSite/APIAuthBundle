<?php
/**
 * Project by CoreSite APIAuth.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 01.04.2016 16:32
 */

namespace CoreSite\APIAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;

class APIAuthFactory extends FormLoginFactory
{
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'cs_apiauth_authentication_provider.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('cs_apiauth_authentication_provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id)
        ;

        return $providerId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = 'cs_apiauth_authentication_listener.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('cs_apiauth_authentication_listener'));

        return $listenerId;

    }

//    protected function getListenerId()
//    {
//        return 'security.authentication.listener.form';
//    }

    public function getKey()
    {
        return 'cs_apiauth_login';
    }

}