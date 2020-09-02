<?php

namespace Ethinking\PushConnector\Connector\Channels\Mapper\Provider;

/**
 * Interface ChannelProviderMapperConfigurationInterface
 * @package Ethinking\PushConnector\Connector\Channels\Mapper\Provider
 */
interface ChannelProviderMapperConfigurationInterface
{
    /**
     * @param string $contentTypeIdentifier
     * @return array|null
     */
    public function channelProviderMapper(string $contentTypeIdentifier): ?array;
}
