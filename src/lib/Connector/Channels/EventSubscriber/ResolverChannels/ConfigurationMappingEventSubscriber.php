<?php

namespace EzPlatform\PushConnector\Connector\Channels\EventSubscriber\ResolverChannels;

use EzPlatform\PushConnector\Connector\Channels\Event\AfterResolverChannelsConfiguration;
use EzPlatform\PushConnector\Connector\Channels\Registry\ChannelsRegistry;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;

/**
 * Class ConfigurationMappingEventSubscriber
 * @package EzPlatform\PushConnector\Connector\Channels\EventSubscriber\ResolverChannels
 */
final class ConfigurationMappingEventSubscriber extends AbstractSubscriber
{
    /** @var \EzPlatform\PushConnector\Connector\Channels\Registry\ChannelsRegistry */
    private $channelsRegistry;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /**
     * ConfigurationMappingEventSubscriber constructor.
     * @param \EzPlatform\PushConnector\Connector\Channels\Registry\ChannelsRegistry $channelsRegistry
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
     * @param \EzPlatform\PushConnector\Connector\Channels\Event\AfterResolverChannelsConfiguration $event
     */
    public function onAfterResolverChannelsMapping(AfterResolverChannelsConfiguration $event)
    {
        //$enabledMapper = $event->getEnabledMapper();
    }
}
