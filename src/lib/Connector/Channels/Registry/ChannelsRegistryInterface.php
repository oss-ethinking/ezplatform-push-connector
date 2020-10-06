<?php

namespace EzPlatform\PushConnector\Connector\Channels\Registry;

use EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface;

/**
 * Interface ChannelsRegistryInterface
 * @package EzPlatform\PushConnector\Connector\Channels\Registry
 */
interface ChannelsRegistryInterface
{
    /**
     * @return PushConnectorChannelsInterface[]
     */
    public function getChannels(): array;

    /**
     * Returns channel corresponding to given identifier.
     *
     * @throws \InvalidArgumentException if no channel exists for $limitationIdentifier
     *
     * @return PushConnectorChannelsInterface
     */
    public function getChannel($dentifier): PushConnectorChannelsInterface;

    /**
     * Checks if a channel exists for given identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasChannel($identifier): bool;

    /**
     * @param PushConnectorChannelsInterface $channel
     */
    public function addChannel(PushConnectorChannelsInterface $channel);
}
