<?php

namespace Ethinking\PushConnectorBundle\Service;

use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Ethinking\EthinkingPushApiBundle\Entity\Settings;
use Ethinking\PushConnectorBundle\Entity\MainSettings;
use Ethinking\PushConnectorBundle\Repository\MainSettingsRepository;

/**
 * Class PushService
 * @package Ethinking\PushConnectorBundle\Service
 */
class PushService
{
    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @param PushApiService $pushApiService
     * @param MainSettingsRepository $mainSettingsRepository
     */
    public function __construct(PushApiService $pushApiService, MainSettingsRepository $mainSettingsRepository)
    {
        /** @var MainSettings $mainSettings */
        $mainSettings = $mainSettingsRepository->findOneBy([]);

        if (!empty($mainSettings)) {
            $settings = new Settings(
                $mainSettings->getSettingsId(),
                $mainSettings->getDomain(),
                $mainSettings->getUsername(),
                $mainSettings->getPassword(),
                $mainSettings->getClientId()
            );

            $this->pushApiService = $pushApiService->getInstance($settings);
        }
    }

    /**
     * @return PushApiInstance
     */
    public function getPushApiService()
    {
        return $this->pushApiService;
    }
}