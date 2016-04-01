<?php

namespace CoreSite\APIAuthBundle;

use CoreSite\APIAuthBundle\DependencyInjection\Security\Factory\APIAuthFactory;
use CoreSite\APIAuthBundle\Firewall\APIAuthListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreSiteAPIAuthBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new APIAuthFactory());
    }
}
