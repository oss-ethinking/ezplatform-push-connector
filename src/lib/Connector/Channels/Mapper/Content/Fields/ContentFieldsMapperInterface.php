<?php

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields;

use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Interface ContentFieldsMapperInterface
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields
 */
interface ContentFieldsMapperInterface
{
    /**
     * @param $identifier
     * @return mixed
     */
    public function support($identifier);

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @return mixed
     */
    public function value(Field $field);

    /**
     * @return string
     */
    public function __toString(): string;
}
