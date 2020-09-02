<?php

namespace Ethinking\PushConnectorBundle\Entity;

use Ethinking\PushConnectorBundle\Service\PushApiService;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Channel
{
    private $id;
    private $platformId;
    private $appName;
    private $pushTemplate;
    private $senderId;
    private $firebaseMessagingSenderId;
    private $firebaseProjectId;
    private $firebaseApiKey;
    private $firebaseAppId;
    private $accessToken;
    private $fallbackUrl;
    private $apiUrl;
    private $serviceWorkerPath;
    private $connectedTagIds;

    const NO_BLANK = 'This value should not be blank';


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id)
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
    public function setPlatformId($platformId)
    {
        $this->platformId = $platformId;
    }

    /**
     * @return mixed
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param mixed $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * @return mixed
     */
    public function getPushTemplate()
    {
        return $this->pushTemplate;
    }

    /**
     * @param mixed $pushTemplate
     */
    public function setPushTemplate($pushTemplate)
    {
        $this->pushTemplate = $pushTemplate;
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param mixed $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @return mixed
     */
    public function getFirebaseMessagingSenderId()
    {
        return $this->firebaseMessagingSenderId;
    }

    /**
     * @param mixed $firebaseMessagingSenderId
     */
    public function setFirebaseMessagingSenderId($firebaseMessagingSenderId)
    {
        $this->firebaseMessagingSenderId = $firebaseMessagingSenderId;
    }

    /**
     * @return mixed
     */
    public function getFirebaseProjectId()
    {
        return $this->firebaseProjectId;
    }

    /**
     * @param mixed $firebaseProjectId
     */
    public function setFirebaseProjectId($firebaseProjectId)
    {
        $this->firebaseProjectId = $firebaseProjectId;
    }

    /**
     * @return mixed
     */
    public function getFirebaseApiKey()
    {
        return $this->firebaseApiKey;
    }

    /**
     * @param mixed $firebaseApiKey
     */
    public function setFirebaseApiKey($firebaseApiKey)
    {
        $this->firebaseApiKey = $firebaseApiKey;
    }

    /**
     * @return mixed
     */
    public function getFirebaseAppId()
    {
        return $this->firebaseAppId;
    }

    /**
     * @param mixed $firebaseAppId
     */
    public function setFirebaseAppId($firebaseAppId)
    {
        $this->firebaseAppId = $firebaseAppId;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getFallbackUrl()
    {
        return $this->fallbackUrl;
    }

    /**
     * @param mixed $fallbackUrl
     */
    public function setFallbackUrl($fallbackUrl): void
    {
        $this->fallbackUrl = $fallbackUrl;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return mixed
     */
    public function getServiceWorkerPath()
    {
        return $this->serviceWorkerPath;
    }

    /**
     * @param mixed $serviceWorkerPath
     */
    public function setServiceWorkerPath($serviceWorkerPath): void
    {
        $this->serviceWorkerPath = $serviceWorkerPath;
    }

    /**
     * @return mixed
     */
    public function getConnectedTagIds()
    {
        return $this->connectedTagIds;
    }

    /**
     * @param mixed $connectedTagIds
     */
    public function setConnectedTagIds($connectedTagIds): void
    {
        $this->connectedTagIds = $connectedTagIds;
    }


    /**
     * @return bool
     */
    public function isWebPush()
    {
        return $this->platformId == PushApiService::WEB_PUSH;
    }

    /**
     * @return string
     */
    public function getPlatformName(): string
    {
        $platformMapping = [
            PushApiService::FIREBASE_ANDROID => 'Firebase(Android)',
            PushApiService::WEB_PUSH => 'Web Push (Chrome & Firefox)',
            PushApiService::IOS => 'iOS',
        ];

        if (!array_key_exists($this->platformId, $platformMapping)) {
            return "Undefined";
        }

        return $platformMapping[$this->platformId];
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('platformId', new Assert\NotBlank());
        $metadata->addPropertyConstraint('appName', new Assert\NotBlank());
        $metadata->addPropertyConstraint('pushTemplate', new Assert\NotBlank());
        $metadata->addPropertyConstraint('senderId', new Assert\NotBlank());

        $webPushValidation = function (Channel $channel, ExecutionContextInterface $context) {
            if (!$channel->isWebPush()) {
                return;
            }

            if (empty(trim($channel->firebaseMessagingSenderId))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('firebaseMessagingSenderId')
                    ->addViolation();
            }

            if (empty(trim($channel->firebaseProjectId))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('firebaseProjectId')
                    ->addViolation();
            }

            if (empty(trim($channel->firebaseApiKey))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('firebaseApiKey')
                    ->addViolation();
            }

            if (empty(trim($channel->firebaseAppId))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('firebaseAppId')
                    ->addViolation();
            }

            if (empty(trim($channel->fallbackUrl))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('fallbackUrl')
                    ->addViolation();
            }

            if (empty(trim($channel->serviceWorkerPath))) {
                $context->buildViolation(Channel::NO_BLANK)
                    ->atPath('serviceWorkerPath')
                    ->addViolation();
            }
        };

        $metadata->addConstraint(new Assert\Callback($webPushValidation));
    }
}
