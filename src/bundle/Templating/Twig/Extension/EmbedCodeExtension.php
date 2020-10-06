<?php

namespace EzPlatform\PushConnectorBundle\Templating\Twig\Extension;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use EzPlatform\PushConnectorBundle\Service\PushService;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Twig\TwigFunction;
use Twig\Environment;

class EmbedCodeExtension extends \Ethinking\EthinkingPushApiBundle\Templating\Twig\Extension\EmbedCodeExtension
{
    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    public function __construct(PushService $pushService)
    {
        $this->pushApiService = $pushService->getPushApiService();
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_push_connector_embed_code',
                [$this, 'generate'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true
                ]
            )];
    }

    public function generate(Environment $environment, $config = []): string
    {
        $channel = $this->getDefaultWebPushChannel();
        return parent::generate($environment, [
            'app' =>
                [
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
                    'firebase' =>
                        [
                            'projectId' => "{$channel->getFirebaseProjectId()}",
                            'apiKey' => "{$channel->getFirebaseApiKey()}",
                            'appId' => "{$channel->getFirebaseAppId()}",
                            'messageSenderId' => "{$channel->getFirebaseMessagingSenderId()}",
                            'serviceWorkerPath' => "/service-worker.js",
                        ],
                ],
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
