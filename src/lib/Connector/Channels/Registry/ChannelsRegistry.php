<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\Connector\Channels\Registry;

use Ethinking\PushConnector\Connector\Channels\Exceptions\ChannelNotFoundException;
use Ethinking\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface;

/**
 * Class ChannelsRegistry
 * @package Ethinking\PushConnector\Connector\Channels\Registry
 */
class ChannelsRegistry implements ChannelsRegistryInterface
{
    /** @var PushConnectorChannelsInterface[] */
    private $connectorChannnels;

    /**
     * ChannelsRegistry constructor.
     *
     * @param PushConnectorChannelsInterface[] $connectorChannnels
     */
    public function __construct(array $connectorChannnels = [])
    {
        $this->connectorChannnels = $connectorChannnels;
    }

    /**
     * @return array|\EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface[]
     */
    public function getChannels(): array
    {
        return $this->connectorChannnels;
    }

    /**
     * @param $identifier
     * @return \Ethinking\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface
     */
    public function getChannel($identifier): PushConnectorChannelsInterface
    {
        if (!$this->hasChannel($identifier)) {
            throw new ChannelNotFoundException($identifier);
        }

        return $this->connectorChannnels[$identifier];
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function hasChannel($identifier): bool
    {
        return isset($this->connectorChannnels[$identifier]);
    }

    /**
     * @param \Ethinking\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface $channel
     */
    public function addChannel(PushConnectorChannelsInterface $channel): void
    {
        $channelName = $channel->getName();
        $this->connectorChannnels[$channelName] = $channel;
    }
}
