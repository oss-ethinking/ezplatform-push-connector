<?php

namespace Ethinking\PushConnectorBundle\Templating\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Ethinking\EthinkingPushApiBundle\Entity\Channel;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\PushConnectorBundle\Exceptions\PushConnectorException;
use Ethinking\PushConnectorBundle\Service\PushService;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\TwigFunction;
use Twig\Environment;

class EmbedCodeExtension extends \Ethinking\EthinkingPushApiBundle\Templating\Twig\Extension\EmbedCodeExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @var string
     */
    private $absoluteUrl;

    public function __construct(EntityManagerInterface $em, PushApiService $pushApiService, UrlHelper $urlHelper)
    {
        $this->em = $em;
        $this->pushApiService = $pushApiService;
        $this->absoluteUrl = $urlHelper->getAbsoluteUrl("/");
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ibexa_push_connector_embed_code',
                [$this, 'generate'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true
                ]
            )];
    }

    public function generate(Environment $environment, $config = []): string
    {
        $pushService = new PushService($this->pushApiService, $this->em);
        $this->pushApiService = $pushService->getPushApiService();

        if (empty($this->pushApiService)) {
            throw new PushConnectorException(
                "Unable to generate push connector embed code because no settings were found. "
                . "Remove the embed code placeholder and fill out main settings form");
        }

        /** @var Channel $channel */
        $channel = $this->pushApiService->getDefaultWebPushChannel();

        if (empty($channel)) {
            return parent::generate($environment, []);
        }

        return parent::generate($environment, [
            'app' => [
                'client' => [
                    'defaultUrl' => "{$channel->getFallbackUrl()}",
                    'autosubscribe' => true,
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
            'ui' => [
                'template' => [
                    'modal' => [
                        'logo' => "{$channel->getFallbackUrl()}/bundles/ezplatformpushconnector/img/push-delivery-logo.jpg"
                    ]
                ]
            ]
        ]);
    }
}