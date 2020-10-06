<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\Connector\Channels\Registry;

use EzPlatform\PushConnector\Connector\Channels\Exceptions\ChannelNotFoundException;
use EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface;

/**
 * Class ChannelsRegistry
 * @package EzPlatform\PushConnector\Connector\Channels\Registry
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
     * @return \EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface
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
     * @param \EzPlatform\PushConnector\Connector\Channels\Provider\PushConnectorChannelsInterface $channel
     */
    public function addChannel(PushConnectorChannelsInterface $channel): void
    {
        $channelName = $channel->getName();
        $this->connectorChannnels[$channelName] = $channel;
    }
}
