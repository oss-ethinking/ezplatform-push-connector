<?php

namespace Ethinking\PushConnector\Connector\Channels\Mapper\Provider;

/**
 * Interface ChannelConfiguration
 * @package Ethinking\PushConnector\Connector\Channels\Mapper\Provider
 */
interface ChannelConfiguration
{
    /**
     * @return mixed
     */
    public function resolve();
}
