<?php

namespace Ethinking\PushConnector\Connector\Channels\Registry;

use Ethinking\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface;

/**
 * Interface ChannelsRegistryInterface
 * @package Ethinking\PushConnector\Connector\Channels\Registry
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
