<?php

namespace EzPlatform\PushConnectorBundle\Controller;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use EzPlatform\PushConnector\Connector\Services\ContentMapperService;
use EzPlatform\PushConnector\Connector\Services\ContentPushService;
use EzPlatform\PushConnector\EzPlatform\UI\Permission\PermissionChecker;
use EzSystems\EzPlatformAdminUi\Notification\TranslatableNotificationHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use eZ\Publish\API\Repository\URLAliasService;

/**
 * Class ContentPushController
 * @package EzPlatform\PushConnectorBundle\Controller
 */
class ContentPushController extends Controller
{
    /** @var LocationService */
    private $locationService;

    /** @var RouterInterface */
    private $router;

    /** @var TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var RequestStack */
    private $requestStack;

    /** @var PermissionChecker */
    private $permissionChecker;

    /** @var ContentMapperService */
    private $contentMapperService;

    /** @var ContentPushService */
    private $contentPushService;

    private $urlAliasService;

    /**
     * @param LocationService $locationService
     * @param RouterInterface $router
     * @param TranslatableNotificationHandlerInterface $notificationHandler
     * @param RequestStack $requestStack
     * @param PermissionChecker $permissionChecker
     * @param ContentMapperService $contentMapperService
     * @param ContentPushService $contentPushService
     * @param URLAliasService $urlAliasService
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
     * @param Request $request
     * @param int $locationId
     * @return RedirectResponse
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnauthorizedException
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

        return null;
    }
}