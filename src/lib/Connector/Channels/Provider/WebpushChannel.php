<?php

namespace Ethinking\PushConnector\Connector\Channels\Provider;

use Ethinking\PushConnectorBundle\Service\PushApiService;
use Psr\Log\LoggerInterface;

class WebpushChannel extends AbstractPushConnectorChannel
{
    /** @var string */
    public const ADAPTER_IDENTIFIER = 'webpush';

    /**
     * @var PushApiService
     */
    private $pushApiService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * WebpushChannel constructor.
     * @param PushApiService $pushApiService
     */
    public function __construct(PushApiService $pushApiService, LoggerInterface $logger)
    {
        $this->pushApiService = $pushApiService;
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
