<?php

namespace Ethinking\PushConnector\Connector\Channels\Exceptions;

class ChannelNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
    /**
     * ChannelNotFoundException constructor.
     * @param $identifier
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($identifier, $code = 0, \Exception $previous = null)
    {
        parent::__construct("No channel connector found for '$identifier'", $code, $previous);
    }
}
