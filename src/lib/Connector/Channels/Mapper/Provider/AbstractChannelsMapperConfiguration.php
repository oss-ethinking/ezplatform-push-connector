<?php

namespace Ethinking\PushConnector\Connector\Channels\Mapper\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Generator;

/**
 * Class AbstractChannelsMapperConfiguration
 * @package Ethinking\PushConnector\Connector\Channels\Mapper\Provider
 */
abstract class AbstractChannelsMapperConfiguration implements ChannelConfiguration, ChannelProviderMapperConfigurationInterface
{
    /** @var string  */
    protected const ARG = 'PushConfig';

    /** @var string  */
    protected const CONTENT_TYPES_PARAMETER_NAME = 'push_config.content_types_map';

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    /**
     * @return mixed
     */
    abstract public function resolve();

    /**
     * @param string $contentTypeIdentifier
     * @return array|null
     */
    abstract public function channelProviderMapper($contentTypeIdentifier): ?array;
}
