<?php

namespace Ethinking\PushConnectorBundle;

use Ethinking\PushConnectorBundle\DependencyInjection\Compiler\ContentFieldsMapperPass;
use Ethinking\PushConnectorBundle\DependencyInjection\Compiler\PushConnectorChannelsProviderPass;
use Ethinking\PushConnectorBundle\DependencyInjection\Configuration\Parser\PushConfigParser;
use Ethinking\PushConnectorBundle\DependencyInjection\Security\PolicyProvider\UIEzPlatformPushBundlePolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformPushConnectorBundle extends Bundle
{
    /** @param \Symfony\Component\DependencyInjection\ContainerBuilder $container */
    public function build(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ezpublish');
        $kernelExtension->addPolicyProvider(new UIEzPlatformPushBundlePolicyProvider($this->getPath()));

        $kernelExtension->addConfigParser(new PushConfigParser());
        $kernelExtension->addDefaultSettings(__DIR__ . '/Resources/config/ezplatform', ['default_settings.yaml']);

        $container->addCompilerPass(new PushConnectorChannelsProviderPass());
    }
}
