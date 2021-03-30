<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\Connector\Services;

use ArrayObject;
use eZ\Publish\API\Repository\Values\Content\Content;
use Ethinking\PushConnector\Connector\Channels\Event\AfterResolverChannelsConfiguration;
use Ethinking\PushConnector\Connector\Channels\Mapper\Provider\ChannelsMapperConfiguration;
use Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry;
use Ethinking\PushConnector\Connector\Channels\Registry\ContentFieldsMapperRegistryInterface;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConfigurationDefinitionService
 * @package Ethinking\PushConnector\Connector\Services
 */
class ConfigurationDefinitionService
{
    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ChannelsRegistry */
    private $channelsRegistry;

    /** @var ChannelsMapperConfiguration */
    private $channelsMapperConfiguration;

    /** @var ContentFieldsMapperRegistryInterface */
    private $contentFieldsMapperRegistry;

    /**
     * ConfigurationDefinitionService constructor.
     * @param TranslatableNotificationHandlerInterface $notificationHandler
     * @param EventDispatcherInterface $eventDispatcher
     * @param ChannelsRegistry $channelsRegistry
     * @param ChannelsMapperConfiguration $channelsMapperConfiguration
     * @param ContentFieldsMapperRegistryInterface $contentFieldsMapperRegistry
     */
    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        EventDispatcherInterface $eventDispatcher,
        ChannelsRegistry $channelsRegistry,
        ChannelsMapperConfiguration $channelsMapperConfiguration,
        ContentFieldsMapperRegistryInterface $contentFieldsMapperRegistry
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelsRegistry = $channelsRegistry;
        $this->channelsMapperConfiguration = $channelsMapperConfiguration;
        $this->contentFieldsMapperRegistry = $contentFieldsMapperRegistry;
    }

    /**
     * @param Content $content
     * @return ArrayObject|null
     */
    public function channelResolverConfiguration(Content $content)
    {
        $contentTypeIdentifier = $content->getContentType()->identifier;
        try {
            $enabledMapper =  $this->channelsMapperConfiguration
                ->resolve()
                ->channelProviderMapper($contentTypeIdentifier);

            // Not configured contentType
            if (!$enabledMapper) {
                return null;
            }
            // e.g check if what you have configured is available or add new parameters
            $enabledMapper = new ArrayObject($enabledMapper);

            // Not AvailableChannelProvider implementation
            if (!$this->isAvailableChannelProvider($enabledMapper)) {
                return null;
            }

            return $enabledMapper;
        } catch (\Exception $e) {
            $this->notificationHandler->error(/** @Ignore */ $e->getMessage());
        }

        return null;
    }

    /**
     * @param $enabledMapper
     * @return bool|null
     */
    private function isAvailableChannelProvider($enabledMapper): ?bool
    {
        $availableChannel = true;
        foreach ($enabledMapper as $channel => $config) {
            if (!$this->channelsRegistry->hasChannel($channel)) {
                $this->notificationHandler->error(
                /** @Desc("Push Connector: The channel provider '%channel%' is not available") */
                    'channel.provider.error',
                    ['%channel%' => $channel],
                    'channels'
                );
                $availableChannel = false;
            }
        }
        return $availableChannel;
    }

    /**
     * @param Content $content
     * @return ArrayObject|null
     */
    public function getSupportedFields(Content $content): ? ArrayObject
    {
        // Get enabled Mapper configurations for this content
        $enabledMapper = $this->channelResolverConfiguration($content);
        if (!$enabledMapper) {
            return null;
        }
        foreach ($enabledMapper as $config) {
            if (!$this->isSupportedFieldTypes($content, $config['fields'])) {
                return null;
            }
        }

        $event = new AfterResolverChannelsConfiguration(
            $enabledMapper->getIterator()
        );
        $this->eventDispatcher->dispatch($event, AfterResolverChannelsConfiguration::AFTER_RESOLVER_CHANNELS_MAPPING);

        return $enabledMapper;
    }

    /**
     * @param Content $content
     * @param $fields
     * @return bool
     */
    public function isSupportedFieldTypes(Content $content, $fields): bool
    {
        $supportedFields = true;
        foreach ($fields as $field) {
            $fieldDefinition = $content->getField($field);
            // The configuration contains unsupported fieldType
            if (!$this->contentFieldsMapperRegistry->hasMapper($fieldDefinition->fieldTypeIdentifier)) {
                $this->notificationHandler->error(
                /** @Desc("Push Connector: '%content_type_identifier%' content field type '%field%' for '%identifier%' is not supported") */
                    'content.field.unsupported',
                    [
                        '%content_type_identifier%' => $content->getContentType()->identifier,
                        '%field%' => $fieldDefinition->fieldTypeIdentifier,
                        '%identifier%' => $fieldDefinition->fieldDefIdentifier,

                    ],
                    'channels'
                );
                $supportedFields = false;
            }
        }
        return $supportedFields;
    }
}
