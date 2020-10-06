<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\Helper;

use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper;

/**
 * Class ImageAssetMapper
 * @package EzPlatform\PushConnector\Connector\Channels\Mapper\Content\Fields\Helper
 */
class ImageAssetMapper
{
    /** @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /**
     * ImageAssetMapper constructor.
     * @param \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper $assetMapper
     */
    public function __construct(
        AssetMapper $assetMapper
    ) {
        $this->assetMapper = $assetMapper;
    }

    /**
     * @return string
     */
    public function getImageAssetContentFieldIdentifier(): string
    {
        return $this->assetMapper->getContentFieldIdentifier();
    }
}
