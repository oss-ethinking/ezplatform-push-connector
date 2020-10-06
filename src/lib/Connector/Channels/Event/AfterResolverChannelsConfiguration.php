<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\Connector\Channels\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AfterResolverChannelsConfiguration
 * @package Ethinking\PushConnector\Connector\Channels\Event
 */
class AfterResolverChannelsConfiguration extends Event
{
    /** @var string  */
    public const AFTER_RESOLVER_CHANNELS_MAPPING = 'after_resolver_channels.mapping';

    /** @var \Iterator */
    private $enabledMapper;

    /**
     * AfterResolverChannelsConfiguration constructor.
     * @param \Iterator $enabledMapper
     */
    public function __construct(
        \Iterator $enabledMapper
    ) {
        $this->enabledMapper = $enabledMapper;
    }

    /**
     * @return \Iterator
     */
    public function getEnabledMapper():\Iterator
    {
        return $this->enabledMapper;
    }
}
