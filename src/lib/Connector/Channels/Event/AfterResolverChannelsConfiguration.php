<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\Connector\Channels\Event;

use Iterator;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AfterResolverChannelsConfiguration
 * @package Ethinking\PushConnector\Connector\Channels\Event
 */
class AfterResolverChannelsConfiguration extends Event
{
    /** @var string  */
    const AFTER_RESOLVER_CHANNELS_MAPPING = 'after_resolver_channels.mapping';

    /** @var Iterator */
    private $enabledMapper;

    /**
     * @param Iterator $enabledMapper
     */
    public function __construct(Iterator $enabledMapper) {
        $this->enabledMapper = $enabledMapper;
    }

    /**
     * @return Iterator
     */
    public function getEnabledMapper(): Iterator
    {
        return $this->enabledMapper;
    }
}
