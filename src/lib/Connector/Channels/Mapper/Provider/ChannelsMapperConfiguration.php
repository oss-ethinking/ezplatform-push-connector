<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Provider;

use Generator;

/**
 * Class ChannelsMapperConfiguration
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Provider
 */
class ChannelsMapperConfiguration extends AbstractChannelsMapperConfiguration
{
    /** @var  */
    private $channelsConfiguration;

    /**
     * @return $this|mixed|null
     */
    public function resolve()
    {
        foreach ($this->configResolver->getParameter(self::CONTENT_TYPES_PARAMETER_NAME) as $identifier => $channels) {
            $this->channelsConfiguration []= [self::ARG => ['contentTypeIdentifier' => $identifier, 'channels' => $channels]];
        }

        if (\count($this->channelsConfiguration) === 0) {
            return null;
        }

        return $this;
    }

    /**
     * @param string $contentTypeIdentifier
     * @return array|null
     */
    public function channelProviderMapper($contentTypeIdentifier): ?array
    {
        foreach ($this->channelsConfiguration as $key => $channel) {
            // contentTypeIdentifier not defined
            if ($channel[self::ARG]['contentTypeIdentifier'] !== $contentTypeIdentifier) {
                continue;
            }

            // no channels configuration
            if (empty($channel[self::ARG]['channels'])) {
                return null;
            }

            // get only enabled channels
            return array_filter(
                $channel[self::ARG]['channels'],
                function ($channel) {
                    return $channel['enabled'] ?? false;
                }
            );
        }
        return null;
    }
}
