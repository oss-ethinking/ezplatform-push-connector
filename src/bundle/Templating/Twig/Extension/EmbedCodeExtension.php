<?php

namespace Ethinking\PushConnectorBundle\Templating\Twig\Extension;

use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\PushConnectorBundle\Service\PushService;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\TwigFunction;
use Twig\Environment;

class EmbedCodeExtension extends \Ethinking\EthinkingPushApiBundle\Templating\Twig\Extension\EmbedCodeExtension
{
    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @var string
     */
    private $absoluteUrl;

    public function __construct(PushService $pushService, UrlHelper $urlHelper)
    {
        $this->pushApiService = $pushService->getPushApiService();
        $this->absoluteUrl = $urlHelper->getAbsoluteUrl("/");
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
        /** @var Channel $channel */
        $channel = $this->pushApiService->getDefaultWebPushChannel();

        if (empty($channel)) {
            return parent::generate($environment, []);
        }

        return parent::generate($environment, [
            'app' => [
                'client' => [
                    'defaultUrl' => "{$channel->getFallbackUrl()}",
                    'defaultSubscribedTags' => implode(', ', $channel->getConnectedTagIds()),
                ],
                'api' => [
                    'id' => "{$channel->getId()}",
                    'platformId' => PushApiService::WEB_PUSH,
                    'baseUrl' => "{$channel->getApiUrl()}/push-api/",
                    'accessToken' => "{$channel->getAccessToken()}",
                ],
                'firebase' => [
                    'projectId' => "{$channel->getFirebaseProjectId()}",
                    'apiKey' => "{$channel->getFirebaseApiKey()}",
                    'appId' => "{$channel->getFirebaseAppId()}",
                    'messageSenderId' => "{$channel->getFirebaseMessagingSenderId()}",
                    'serviceWorkerPath' => "/service-worker.js",
                ],
            ],
        ]);
    }
}
