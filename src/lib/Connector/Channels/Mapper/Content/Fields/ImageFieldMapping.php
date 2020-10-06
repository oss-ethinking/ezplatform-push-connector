<?php

namespace Ethinking\PushConnector\Connector\Channels\Mapper\Content\Fields;

use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Class ImageFieldMapping
 * @package Ethinking\PushConnector\Connector\Channels\Mapper\Content\Fields
 */
class ImageFieldMapping extends AbstractContentFieldsMapping
{
    /**
     * @param $identifier
     * @return mixed|void
     */
    public function support($identifier)
    {
        // TODO: Implement support() method.
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @return mixed|string
     */
    public function value(Field $field)
    {
        /** @var \eZ\Publish\Core\FieldType\Image\Value $fieldValue */
        $fieldValue = $field->value;

        return $fieldValue->uri;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'ezimage';
    }
}
