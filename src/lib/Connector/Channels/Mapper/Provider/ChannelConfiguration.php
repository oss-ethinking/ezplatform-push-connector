<?php

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Provider;

/**
 * Interface ChannelConfiguration
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Provider
 */
interface ChannelConfiguration
{
    /**
     * @return mixed
     */
    public function resolve();
}
