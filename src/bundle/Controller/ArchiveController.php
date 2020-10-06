<?php

/** Some experiments first */

namespace EzPlatform\PushConnectorBundle\Controller;

use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use EzPlatform\PushConnectorBundle\Service\PushService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArchiveController
 * @package EzPlatform\PushConnectorBundle\Controller
 */
class ArchiveController extends Controller
{
    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @param PushService $pushService
     */
    public function __construct(PushService $pushService)
    {
        $this->pushApiService = $pushService->getPushApiService();
    }

    /**
     * @return Response
     */
    public function indexAction()
    {
        $history = $this->pushApiService->getHistory();
        return $this->render('@ezdesign/archive.html.twig', [
            'history' => $history
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function detailsAction($id)
    {
        $details = $this->pushApiService->getHistoryDetails($id);
        return $this->render('@ezdesign/archive_details.html.twig', [
            'details' => $details
        ]);
    }

    /**
     * @param $page
     * @return Response
     */
    public function moreArchiveAction($page)
    {
        $history = $this->pushApiService->getHistory($page);
        return $this->render('@ezdesign/more_archive.html.twig', [
            'history' => $history
        ]);
    }
}
