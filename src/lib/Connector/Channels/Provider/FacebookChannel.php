<?php

namespace EzPlatform\PushConnector\Connector\Channels\Provider;

class FacebookChannel extends AbstractPushConnectorChannel
{
    public const ADAPTER_IDENTIFIER = 'facebook';

    public function support()
    {
    }

    public function send(object $fields, string $articleUrl)
    {
       
    }

    public function getName(): string
    {
        return self::ADAPTER_IDENTIFIER;
    }
}
