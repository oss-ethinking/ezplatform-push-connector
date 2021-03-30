<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\EzPlatform\UI\Permission;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;

/**
 * Class PermissionChecker
 * @package Ethinking\PushConnector\EzPlatform\UI\Permission
 */
class PermissionChecker
{
    /** @var PermissionResolver */
    private $permissionResolver;

    /**
     * PermissionChecker constructor.
     * @param PermissionResolver $permissionResolver
     */
    public function __construct(PermissionResolver $permissionResolver) {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @param $module
     * @param $function
     * @return bool
     * @throws InvalidArgumentException
     */
    public function checkUserAccess($module, $function): bool
    {
        return $this->permissionResolver->hasAccess($module, $function);
    }
}
