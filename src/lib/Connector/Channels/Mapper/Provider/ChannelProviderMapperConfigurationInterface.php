<?php

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Provider;

/**
 * Interface ChannelProviderMapperConfigurationInterface
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Provider
 */
interface ChannelProviderMapperConfigurationInterface
{
    /**
     * @param string $contentTypeIdentifier
     * @return array|null
     */
    public function channelProviderMapper(string $contentTypeIdentifier): ?array;
}
