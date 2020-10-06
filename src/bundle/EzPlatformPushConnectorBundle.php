<?php

namespace EzPlatform\PushConnectorBundle;

use EzPlatform\PushConnectorBundle\DependencyInjection\Compiler\ContentFieldsMapperPass;
use EzPlatform\PushConnectorBundle\DependencyInjection\Compiler\PushConnectorChannelsProviderPass;
use EzPlatform\PushConnectorBundle\DependencyInjection\Configuration\Parser\PushConfigParser;
use EzPlatform\PushConnectorBundle\DependencyInjection\Security\PolicyProvider\UIEzPlatformPushBundlePolicyProvider;
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
