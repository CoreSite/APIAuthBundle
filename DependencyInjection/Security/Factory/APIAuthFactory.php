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

/**
 * Class APIAuthFactory
 *
 * Регистрация в системе нового типа аутификации
 *
 * @package CoreSite\APIAuthBundle\DependencyInjection\Security\Factory
 */
class APIAuthFactory extends FormLoginFactory
{
    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPointId)
    {
        $providerId = 'fcs_apiauth.authentication.provider.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('cs_apiauth.authentication.provider'))
            //->replaceArgument(2, new Reference($userProviderId))
            ->replaceArgument(1, $id)
        ;

        $listenerId = 'cs_apiauth_authentication_listener.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('cs_apiauth_authentication_listener'));

        $entryPointId = $this->createEntryPoint($container, $id, $config, $defaultEntryPointId);

        return [$providerId, $listenerId, $entryPointId];

    }

    public function getKey()
    {
        return 'cs_apiauth_login';
    }

    protected function getListenerId()
    {
        return 'security.authentication.listener.form';
    }

    public function getPosition()
    {
        return 'pre_auth';
    }


}