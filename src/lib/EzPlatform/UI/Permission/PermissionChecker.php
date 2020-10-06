<?php

declare(strict_types=1);

namespace EzPlatform\PushConnector\EzPlatform\UI\Permission;

use eZ\Publish\API\Repository\PermissionResolver;

/**
 * Class PermissionChecker
 * @package EzPlatform\PushConnector\EzPlatform\UI\Permission
 */
class PermissionChecker
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver*/
    private $permissionResolver;

    /**
     * PermissionChecker constructor.
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(
        PermissionResolver $permissionResolver
    ) {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @param $module
     * @param $function
     * @return bool
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function checkUserAccess($module, $function): bool
    {
        if (!$this->permissionResolver->hasAccess($module, $function)) {
            return false;
        }
        return true;
    }
}
