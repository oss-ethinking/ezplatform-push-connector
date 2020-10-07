<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\Connector\Services;

use Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry;

/**
 * Class ContentPushService
 * @package Ethinking\PushConnector\Connector\Services
 */
class ContentPushService
{
    /** @var \Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry */
    private $channelsRegistry;

    /**
     * ContentPushService constructor.
     * @param \Ethinking\PushConnector\Connector\Channels\Registry\ChannelsRegistry $channelsRegistry
     */
    public function __construct(
        ChannelsRegistry $channelsRegistry
    ) {
        $this->channelsRegistry = $channelsRegistry;
    }

    /**
     * @todo do we need here a main EventDispatcher after pushing?
     * @param $channelsFieldsValueMapping
     */
    public function pushContent($channelsFieldsValueMapping, $articleUrl)
    {
        $this->getFieldsConfigurationDefinition($channelsFieldsValueMapping, $articleUrl);
    }

    /**
     * @todo Method name contains "get" but doesn't return any data but saves
     * @param $enabledMapper
     * @param $articleUrl
     */
    private function getFieldsConfigurationDefinition($enabledMapper, $articleUrl): void
    {
        foreach ($enabledMapper as $channel => $fields) {
            $this->pushWithProvider($channel, (object)$fields, $articleUrl);
        }
    }

    /**
     * @todo what should be returned or use the provider to save response data in DB
     * @param $channel
     * @param $fields
     * @param $articleUrl
     */
    private function pushWithProvider($channel, $fields, $articleUrl)
    {
        // @todo getChannel() already contains hasChannel() method
        if ($this->channelsRegistry->hasChannel($channel)) {
            $this->channelsRegistry->getChannel($channel)->send($fields, $articleUrl);
        }
    }
}
