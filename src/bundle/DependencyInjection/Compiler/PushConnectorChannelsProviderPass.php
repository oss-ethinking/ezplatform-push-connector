<?php

namespace Ethinking\PushConnectorBundle\DependencyInjection\Compiler;

use Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PushConnectorChannelsProviderPass implements CompilerPassInterface
{
    public const PUSH_CONNECTOR_CHANNELS = 'ezplatform.push_connector.channels';

    public function process(ContainerBuilder $container)
    {
        //check if ChannelsRegistry exist
        if (!$container->hasDefinition(ChannelsRegistry::class)) {
            return;
        }

        //get the ChannelsRegistry definition
        $channelsRegistryDefinition = $container->getDefinition(ChannelsRegistry::class);

        //find all services tagged with "ezplatform.push_connector.channels"
        $taggedChannelServices = $container->findTaggedServiceIds(self::PUSH_CONNECTOR_CHANNELS);

        //loop through the taggedServices and pass a reference to each notification into it.
        foreach ($taggedChannelServices as $id => $tags) {
            $channelsRegistryDefinition->addMethodCall('addChannel', array(new Reference(($id))));
        }
    }
}
