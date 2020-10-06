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
    /** @var \Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry */
    private $channelsRegistry;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /**
     * ConfigurationMappingEventSubscriber constructor.
     * @param \Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry $channelsRegistry
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
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
     * @param \Ethinking\PushConnector\Connector\Channels\Event\AfterResolverChannelsConfiguration $event
     */
    public function onAfterResolverChannelsMapping(AfterResolverChannelsConfiguration $event)
    {
        //$enabledMapper = $event->getEnabledMapper();
    }
}
