<?php

namespace Ethinking\PushConnector\Connector\Channels\Registry;

use Ethinking\PushConnector\Connector\Channels\Mapper\Content\Fields\ContentFieldsMapperInterface;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;

/**
 * Interface ContentFieldsMapperRegistryInterface
 * @package Ethinking\PushConnector\Connector\Channels\Registry
 */
interface ContentFieldsMapperRegistryInterface
{

    /**
     * @param ContentFieldsMapperInterface $mappers iterable of ContentFieldsMapper
     */
    public function __construct(iterable $mappers);

    /**
     * Returns the ContentFieldsMapper matching the argument.
     *
     * @param string $identifier An identifier string.
     * @return ContentFieldsMapperInterface
     * @throws NotFoundException If no ContentFieldsMapper exists with this identifier
     */
    public function getMapper($identifier): ContentFieldsMapperInterface;

    /**
     * @param $identifier
     * @return bool
     */
    public function hasMapper($identifier): bool;

    /**
     * Returns the identifiers of all registered ContentFieldsMapper.
     *
     * @return string[] Array of identifier strings.
     */
    public function getMappers(): array;
}
