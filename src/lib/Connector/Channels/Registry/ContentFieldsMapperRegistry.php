<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\Connector\Channels\Registry;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\ContentFieldsMapperInterface;

/**
 * Class ContentFieldsMapperRegistry
 * @package EzPlatform\PushConnector\Connector\Channels\Registry
 */
class ContentFieldsMapperRegistry implements ContentFieldsMapperRegistryInterface
{
    /** @var \EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\ContentFieldsMapperInterface */
    private $mappers = [];

    /**
     * @param ContentFieldsMapperInterface $mappers iterable of ContentFieldsMapper
     */
    public function __construct(iterable $mappers)
    {
        foreach ($mappers as $mapper) {
            $this->mappers[(string)$mapper] = $mapper;
        }
    }

    /**
     * @param string $identifier
     * @return ContentFieldsMapperInterface
     * @throws NotFoundException
     */
    public function getMapper($identifier): ContentFieldsMapperInterface
    {
        if (array_key_exists($identifier, $this->mappers)) {
            return $this->mappers[$identifier];
        }
        throw new NotFoundException('The ContentFieldsMapper could not be found.', $identifier);
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function hasMapper($identifier): bool
    {
        return isset($this->mappers[$identifier]);
    }

    /**
     * @return array
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }
}
