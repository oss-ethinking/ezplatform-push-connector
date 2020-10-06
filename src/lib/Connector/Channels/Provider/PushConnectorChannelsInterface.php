<?php

namespace EzPlatform\PushConnector\Connector\Channels\Provider;

/**
 * Interface PushConnectorChannelsInterface
 * @package EzPlatform\PushConnector\Connector\Channels\Provider
 */
interface PushConnectorChannelsInterface
{
    /**
     * @return mixed
     */
    public function support();

    /**
     * @param object $fields
     * @param string $articleUrl
     * @return mixed
     */
    public function send(object $fields, string $articleUrl);

    /**
     * @return string
     */
    public function getName(): string;
}
