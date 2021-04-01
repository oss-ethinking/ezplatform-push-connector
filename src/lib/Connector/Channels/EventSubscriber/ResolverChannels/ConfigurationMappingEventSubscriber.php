<?php

namespace Ethinking\PushConnector\Connector\Channels\EventSubscriber\ResolverChannels;

use Ethinking\PushConnector\Connector\Channels\Event\AfterResolverChannelsConfiguration;
use Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;

/**
 * Class ConfigurationMappingEventSubscriber
 * @package Ethinking\PushConnector\Connector\Channels\EventSubscriber\ResolverChannels
 */
final class ConfigurationMappingEventSubscriber extends AbstractSubscriber
{
    /** @var ChannelsRegistry */
    private $channelsRegistry;

    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /**
     * ConfigurationMappingEventSubscriber constructor.
     * @param ChannelsRegistry $channelsRegistry
     * @param TranslatableNotificationHandlerInterface $notificationHandler
     */
    public function __construct(
        ChannelsRegistry $channelsRegistry,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->channelsRegistry = $channelsRegistry;
        $this->notificationHandler = $notificationHandler;
    }

    /** @return array */
    public static function getSubscribedEvents()
    {
        return [
            AfterResolverChannelsConfiguration::AFTER_RESOLVER_CHANNELS_MAPPING =>
            [
                ['onAfterResolverChannelsMapping']
            ]
        ];
    }

    /**
     * @param AfterResolverChannelsConfiguration $event
     */
    public function onAfterResolverChannelsMapping(AfterResolverChannelsConfiguration $event)
    {
    }
}
