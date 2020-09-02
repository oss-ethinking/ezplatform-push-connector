<?php

namespace Ethinking\PushConnectorBundle\Controller;

use eZ\Publish\API\Repository\LocationService;
use Ethinking\PushConnector\Connector\Services\ContentMapperService;
use Ethinking\PushConnector\Connector\Services\ContentPushService;
use Ethinking\PushConnector\EzPlatform\UI\Permission\PermissionChecker;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\API\Repository\URLAliasService;

/**
 * Class ContentPushController
 * @package Ethinking\PushConnectorBundle\Controller
 */
class ContentPushController extends Controller
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ethinking\PushConnector\EzPlatform\UI\Permission\PermissionChecker */
    private $permissionChecker;

    /** @var \Ethinking\PushConnector\Connector\Services\ContentMapperService */
    private $contentMapperService;

    /** @var \Ethinking\PushConnector\Connector\Services\ContentPushService */
    private $contentPushService;

    private $urlAliasService;

    /**
     * ContentPushController constructor.
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Ethinking\PushConnector\EzPlatform\UI\Permission\PermissionChecker $permissionChecker
     */
    public function __construct(
        LocationService $locationService,
        RouterInterface $router,
        TranslatableNotificationHandlerInterface $notificationHandler,
        RequestStack $requestStack,
        PermissionChecker $permissionChecker,
        ContentMapperService $contentMapperService,
        ContentPushService $contentPushService,
        URLAliasService $urlAliasService

    )
    {
        $this->locationService = $locationService;
        $this->router = $router;
        $this->notificationHandler = $notificationHandler;
        $this->requestStack = $requestStack;
        $this->permissionChecker = $permissionChecker;
        $this->contentMapperService = $contentMapperService;
        $this->contentPushService = $contentPushService;
        $this->urlAliasService = $urlAliasService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $locationId
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function pushContentAction(Request $request, int $locationId)
    {
        $location = $this->locationService->loadLocation($locationId);
        $contentId = (int)$location->contentInfo->id;

        if (!$this->permissionChecker->checkUserAccess('push', 'content_create_push')) {
            $this->notificationHandler->warning(
                'push.view.access_denied',
                [],
                'views'
            );
            return new RedirectResponse($this->router->generate('_ez_content_view', ['contentId' => $contentId]));
        }

        try {

            //Check the supported Content field mapping
            $channelsFieldsValueMapping = $this->contentMapperService->getFieldsMappingFieldValue($location->getContent());
            $urlPath = $this->urlAliasService->reverseLookup($location)->path;
            $this->contentPushService->pushContent($channelsFieldsValueMapping, (string)$urlPath);

            //@todo EventListener on Success?
            $this->notificationHandler->success(
                'push.share.success',
                [
                    '%contentName%' => $location->contentInfo->name,
                ],
                'channels'
            );

            return new RedirectResponse($this->router->generate('_ez_content_view', ['contentId' => $contentId]));
        } catch (\Exception $e) {
            $this->notificationHandler->error($e->getMessage());
        }
    }
}