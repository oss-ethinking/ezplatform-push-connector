<?php
namespace Ethinking\PushConnectorBundle\Controller;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Ethinking\PushConnectorBundle\Service\PushService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class WebPushController
 * @package Ethinking\EthinkingPushApiBundle\Controller
 */
class JsLibraryController
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    public function __construct(KernelInterface $kernel, PushService $pushService)
    {
        $this->projectDir = $kernel->getProjectDir();
        $this->pushApiService = $pushService->getPushApiService();
    }

    /**
     * @return BinaryFileResponse
     */
    public function webpushAction()
    {
        $path = $this->projectDir .
            '/vendor/ethinking/push-api/src/bundle/Resources/public/js/scripts/webpush.js';

        return new BinaryFileResponse($path, Response::HTTP_OK, [
            'Content-Type' => 'text/javascript'
        ]);
    }

    /**
     * @return Response
     */
    public function serviceWorkerAction()
    {
        $channel = $this->getDefaultWebPushChannel();
        $config = [
            'app' => [
                'client' => [
                    'defaultUrl' => "{$channel->getFallbackUrl()}",
                    'defaultSubscribedTags' => implode(', ', $channel->getConnectedTagIds()),
                ],
                'api' => [
                    'id' => "{$channel->getId()}",
                    'platformId' => PushApiService::WEB_PUSH,
                    'baseUrl' => "{$channel->getApiUrl()}",
                    'accessToken' => "{$channel->getAccessToken()}",
                ],
                'firebase' => [
                    'projectId' => "{$channel->getFirebaseProjectId()}",
                    'apiKey' => "{$channel->getFirebaseApiKey()}",
                    'appId' => "{$channel->getFirebaseAppId()}",
                    'messageSenderId' => "{$channel->getFirebaseMessagingSenderId()}",
                    'serviceWorkerPath' => "/service-worker.js",
                ],
                'database' => [
                    'notificationDB' => 'ethinking-notification',
                    'notificationTable' => 'notification',
                    'version' => 1,
                    'notificationMaxResults' => 50
                ]
            ],
        ];

        $configJson = json_encode($config);
        if ($configJson === false) {
            return new Response("", Response::HTTP_NOT_FOUND);
        }

        $js = <<<EOD
var config = $configJson;

importScripts('/assets/ezplatform/build/ethinking-push-api-service-worker-js.js');  
EOD;

        return new Response($js, Response::HTTP_OK, [
            'Content-Type' => 'text/javascript'
        ]);
    }

    /**
     * @return Channel | NULL
     */
    private function getDefaultWebPushChannel()
    {
        /** @var Channel $channel */
        $channel = $this->pushApiService->getDefaultWebPushChannel();

        return $channel ?? new Channel();
    }
}