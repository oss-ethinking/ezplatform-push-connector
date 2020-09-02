<?php

/** Some experiments first */

namespace Ethinking\PushConnectorBundle\Controller;

use Ethinking\PushConnectorBundle\Service\PushApiService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArchiveController
 * @package Ethinking\PushConnectorBundle\Controller
 */
class ArchiveController extends Controller
{
    /**
     * @var PushApiService
     */
    private $pushApiService;

    /**
     * @param PushApiService $pushApiService
     */
    public function __construct(PushApiService $pushApiService)
    {
        $this->pushApiService = $pushApiService;
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
