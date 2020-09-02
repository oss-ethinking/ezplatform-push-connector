<?php

namespace Ethinking\PushConnectorBundle\Entity;

use Ethinking\PushConnectorBundle\Service\PushApiService;

class HistoryDetails
{
    private $id;
    private $platformId;
    private $state;
    private $totalCount;
    private $successCount;

    /**
     * @param $id
     * @param $platformId
     * @param $state
     * @param $totalCount
     * @param $successCount
     */
    public function __construct($id, $platformId, $state, $totalCount, $successCount)
    {
        $this->id = $id;
        $this->platformId = $platformId;
        $this->state = $state;
        $this->totalCount = $totalCount;
        $this->successCount = $successCount;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * @param mixed $platformId
     */
    public function setPlatformId($platformId): void
    {
        $this->platformId = $platformId;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param mixed $totalCount
     */
    public function setTotalCount($totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @return mixed
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * @param mixed $successCount
     */
    public function setSuccessCount($successCount): void
    {
        $this->successCount = $successCount;
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        $platformMapping = [
            PushApiService::FIREBASE_ANDROID => 'Android',
            PushApiService::WEB_PUSH => 'Web Push',
            PushApiService::IOS => 'iOS',
        ];

        if (!array_key_exists($this->platformId, $platformMapping)) {
            return "Undefined";
        }

        return $platformMapping[$this->platformId];
    }
}
