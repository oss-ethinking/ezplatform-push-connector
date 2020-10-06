<?php

namespace EzPlatform\PushConnector\Connector\Channels\Provider;

use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use EzPlatform\PushConnectorBundle\Service\PushService;
use Psr\Log\LoggerInterface;

class WebpushChannel extends AbstractPushConnectorChannel
{
    /** @var string */
    public const ADAPTER_IDENTIFIER = 'webpush';

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PushService $pushService
     * @param LoggerInterface $logger
     */
    public function __construct(PushService $pushService, LoggerInterface $logger)
    {
        $this->pushApiService = $pushService->getPushApiService();
        $this->logger = $logger;
    }

    /**
     * @return mixed|void
     */
    public function support()
    {
    }

    /**
     * @param object $fields
     * @param string $articleUrl
     * @return mixed|void
     */
    public function send(object $fields,string $articleUrl)
    {
        $fieldsArray=(array)$fields;
        $this->pushApiService->sendPushNotification($fieldsArray += ['url' => $articleUrl]);
        $this->logger->info(self::ADAPTER_IDENTIFIER . ' -> start pushing');
        //@todo do we need here a specific EventDispatcher after each pushing?
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::ADAPTER_IDENTIFIER;
    }
}
