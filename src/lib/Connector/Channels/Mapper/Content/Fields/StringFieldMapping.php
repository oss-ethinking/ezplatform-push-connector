<?php

namespace Ethinking\PushConnector\Connector\Channels\Mapper\Content\Fields;

use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Class StringFieldMapping
 * @package Ethinking\PushConnector\Connector\Channels\Mapper\Content\Fields
 */
class StringFieldMapping extends AbstractContentFieldsMapping
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
     * @return string
     */
    public function value(Field $field):string
    {
        /** @var \eZ\Publish\Core\FieldType\TextLine\Value $fieldValue */
        $fieldValue = $field->value;

        return $fieldValue->text;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'ezstring';
    }
}
