<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\Helper\ImageAssetMapper;

/**
 * Class AbstractContentFieldsMapping
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields
 */
abstract class AbstractContentFieldsMapping implements ContentFieldsMapperInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\Helper\ImageAssetMapper */
    protected $imageAssetMapper;

    /**
     * AbstractContentFieldsMapping constructor.
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\Helper\ImageAssetMapper $imageAssetMapper
     */
    public function __construct(
        ContentService $contentService,
        ImageAssetMapper $imageAssetMapper
    ) {
        $this->contentService = $contentService;
        $this->imageAssetMapper = $imageAssetMapper;
    }

    /**
     * @param $id
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    protected function getContent($id): Content
    {
        return $this->contentService->loadContent($id);
    }
}
