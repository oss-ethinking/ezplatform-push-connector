<?php

namespace Ethinking\PushConnectorBundle\Service;

use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Part\AbstractMultipartPart;
use Symfony\Component\Mime\Part\TextPart;

final class FormDataPart extends AbstractMultipartPart
{
    const FORM_DATA = 'form-data';

    private $fields = [];

    /**
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        parent::__construct();

        foreach ($fields as $name => $value) {
            if (!is_string($value) && !is_array($value) && !$value instanceof TextPart && !$value instanceof JsonPart) {
                throw new InvalidArgumentException(sprintf(
                    'A form field value can only be a string, an array, or an instance of JsonPart '
                    . 'or TextPart ("%s" given).', is_object($value) ? get_class($value) : gettype($value)));
            }

            $this->fields[$name] = $value;
        }
        // HTTP does not support \r\n in header values
        $this->getHeaders()->setMaxLineLength(PHP_INT_MAX);
    }

    public function getMediaSubtype(): string
    {
        return self::FORM_DATA;
    }

    public function getParts(): array
    {
        return $this->prepareFields($this->fields);
    }

    private function prepareFields(array $fields): array
    {
        $values = [];

        $prepare = function ($item, $key, $root = null) use (&$values, &$prepare) {
            $fieldName = $root ? sprintf('%s[%s]', $root, $key) : $key;

            if (is_array($item)) {
                array_walk($item, $prepare, $fieldName);

                return;
            }

            if ($item instanceof JsonPart) {
                $values[] = $this->prepareJsonPart($fieldName, $item);
            } else {
                $values[] = $this->prepareTextPart($fieldName, $item);
            }
        };

        array_walk($fields, $prepare);

        return $values;
    }

    private function prepareJsonPart(string $name, $value): JsonPart
    {
        if (is_string($value)) {
            return $this->configureJsonPart(
                $name,
                new JsonPart($value, 'utf-8', 'json', '8bit')
            );
        }

        return $this->configureJsonPart($name, $value);
    }

    private function configureJsonPart(string $name, JsonPart $part): JsonPart
    {
        static $r;

        if (null === $r) {
            $r = new \ReflectionProperty(JsonPart::class, 'encoding');
            $r->setAccessible(true);
        }

        $part->setDisposition(self::FORM_DATA);
        $part->setName($name);
        // HTTP does not support \r\n in header values
        $part->getHeaders()->setMaxLineLength(PHP_INT_MAX);
        $r->setValue($part, '8bit');

        return $part;
    }

    private function prepareTextPart(string $name, $value): TextPart
    {
        if (is_string($value)) {
            return $this->configureTextPart(
                $name,
                new TextPart($value, 'utf-8', 'plain', '8bit')
            );
        }

        return $this->configureTextPart($name, $value);
    }

    private function configureTextPart(string $name, TextPart $part): TextPart
    {
        static $r;

        if (null === $r) {
            $r = new \ReflectionProperty(TextPart::class, 'encoding');
            $r->setAccessible(true);
        }

        $part->setDisposition(self::FORM_DATA);
        $part->setName($name);
        // HTTP does not support \r\n in header values
        $part->getHeaders()->setMaxLineLength(PHP_INT_MAX);
        $r->setValue($part, '8bit');

        return $part;
    }
}
