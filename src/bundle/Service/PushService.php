<?php

namespace Ethinking\PushConnectorBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use Ethinking\EthinkingPushApiBundle\Service\PushApiService;
use Ethinking\EthinkingPushApiBundle\Entity\Settings;
use Ethinking\PushConnectorBundle\Entity\MainSettings;

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
     * @param EntityManagerInterface $em
     */
    public function __construct(PushApiService $pushApiService, EntityManagerInterface $em)
    {
        $mainSettingsRepository = $em->getRepository(MainSettings::class);
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