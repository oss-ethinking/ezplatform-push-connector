<?php

namespace Ethinking\PushConnectorBundle\Service;

use Exception;
use Ethinking\PushConnectorBundle\Entity\Channel;
use Ethinking\PushConnectorBundle\Entity\History;
use Ethinking\PushConnectorBundle\Entity\HistoryDetails;
use Ethinking\PushConnectorBundle\Entity\MainSettings;
use Ethinking\PushConnectorBundle\Repository\MainSettingsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PushApiService
 * @package Ethinking\PushConnectorBundle\Service
 */
class PushApiService
{

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var MainSettingsRepository
     */
    private $mainSettingsRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MainSettings|null
     */
    private $settings;

    /**
     * Platforms
     */
    const FIREBASE_ANDROID = '15';
    const WEB_PUSH = '7';
    const IOS = '3';

    const SOURCE_ID = 'sourceId';
    const FALLBACK_URL = 'fallbackUrl';
    const API_USER = 'apiUser';
    const PLATFORM_ID = 'platformId';
    const SENDER_ID = 'senderId';
    const PUSH_TEMPLATE = 'pushTemplate';
    const FIREBASE_MESSAGING_SENDER_ID = 'firebaseMessagingSenderId';
    const FIREBASE_PROJECT_ID = 'firebaseProjectId';
    const FIREBASE_API_KEY = 'firebaseApiKey';
    const FIREBASE_APP_ID = 'firebaseAppId';
    const PARAMETERS = 'parameters';


    /**
     * @param HttpClientInterface $httpClient
     * @param MainSettingsRepository $mainSettingsRepository
     * @param LoggerInterface|null $logger
     * @throws Exception
     */
    public function __construct(HttpClientInterface $httpClient,
                                MainSettingsRepository $mainSettingsRepository,
                                LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->mainSettingsRepository = $mainSettingsRepository;
        $this->logger = $logger;
        $this->settings = $this->mainSettingsRepository->findOneBy([]);

        if (empty($this->settings)) {
            $this->logger->error("no settings");
        }
    }

    /**
     * @return MainSettings|null
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return Channel[]
     */
    public function getChannels(): array
    {
        $channels = [];
        $uri = "/push-admin-api/app/get/{$this->settings->getClientId()}";
        $items = $this->get($uri);

        foreach ($items as $item) {
            $channel = new Channel();
            $channel->setAppName($item['name']);
            $channel->setPlatformId($item[self::PLATFORM_ID]);

            if (array_key_exists('id', $item)) {
                $channel->setId($item['id']);
            }

            if (array_key_exists(self::PARAMETERS, $item)) {
                if (array_key_exists(self::SENDER_ID, $item[self::PARAMETERS])) {
                    $channel->setSenderId($item[self::PARAMETERS][self::SENDER_ID]);
                }

                if (array_key_exists(self::PUSH_TEMPLATE, $item[self::PARAMETERS])) {
                    $channel->setPushTemplate($item[self::PARAMETERS][self::PUSH_TEMPLATE]);
                }

                if (array_key_exists(self::FIREBASE_MESSAGING_SENDER_ID, $item[self::PARAMETERS])) {
                    $channel->setFirebaseMessagingSenderId($item[self::PARAMETERS][self::FIREBASE_MESSAGING_SENDER_ID]);
                }

                if (array_key_exists(self::FIREBASE_PROJECT_ID, $item[self::PARAMETERS])) {
                    $channel->setFirebaseProjectId($item[self::PARAMETERS][self::FIREBASE_PROJECT_ID]);
                }

                if (array_key_exists(self::FIREBASE_API_KEY, $item[self::PARAMETERS])) {
                    $channel->setFirebaseApiKey($item[self::PARAMETERS][self::FIREBASE_API_KEY]);
                }

                if (array_key_exists(self::FIREBASE_APP_ID, $item[self::PARAMETERS])) {
                    $channel->setFirebaseAppId($item[self::PARAMETERS][self::FIREBASE_APP_ID]);
                }

                if (array_key_exists(self::FALLBACK_URL, $item[self::PARAMETERS])) {
                    $channel->setFallbackUrl($item[self::PARAMETERS][self::FALLBACK_URL]);
                }

                $channel->setApiUrl($this->settings->getDomain());

                if (array_key_exists('serviceWorkerPath', $item[self::PARAMETERS])) {
                    $channel->setServiceWorkerPath("{$channel->getFallbackUrl()}/service-worker.js");
                }
            }
            if (array_key_exists(self::API_USER, $item) && array_key_exists('accessToken', $item[self::API_USER])) {
                $channel->setAccessToken($item[self::API_USER]['accessToken']);
            }

            $channel->setConnectedTagIds($this->getConnectedTagIds());

            $channels[] = $channel;
        }

        return $channels;
    }

    /**
     * @param int $id
     * @return Channel|null
     */
    public function getChannel(int $id): ?Channel
    {
        $channels = $this->getChannels();

        foreach ($channels as $channel) {
            if ($channel->getId() == $id) {
                return $channel;
            }
        }

        return null;
    }

    /**
     * @param Channel $channel
     * @return bool
     */
    public function addChannelAndDefaultTag(Channel $channel)
    {
        $this->addTag("General", $this->getDefaultTagSourceId());
        return $this->addChannel($channel);
    }

    /**
     * @param Channel $channel
     * @return bool
     */
    public function addChannel(Channel $channel)
    {
        $uri = "/push-admin-api/app/add/{$this->settings->getClientId()}";
        return $this->post($uri, $this->getChannelFormData($channel), false);
    }

    /**
     * @param Channel $channel
     * @return bool
     */
    public function updateChannel(Channel $channel)
    {
        $uri = "/push-admin-api/app/update/{$this->settings->getClientId()}";
        return $this->post($uri, $this->getChannelFormData($channel, false), false);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function deleteChannel(string $id)
    {
        $uri = "/push-admin-api/app/delete/{$this->settings->getClientId()}";
        return $this->post($uri, ['id' => $id]);
    }

    /**
     * @param int $page
     * @param int $count
     * @return History[]
     */
    public function getHistory($page = 1, $count = 20): array
    {
        $historyArray = [];
        $uri = "/push-admin-api/history/get/{$this->settings->getClientId()}?page={$page}&count={$count}";
        $items = $this->get($uri);

        foreach ($items as $item) {
            $history = new History($item['id'], $item['userName'], $item['status'], $item['time']);
            $historyArray[] = $history;
        }

        return $historyArray;
    }

    /**
     * @param int $id
     * @return HistoryDetails[]
     */
    public function getHistoryDetails($id): array
    {
        $detailsArray = [];
        $uri = "/push-api/status/${id}";
        $items = $this->get($uri);

        foreach ($items as $item) {
            $details = new HistoryDetails(
                $item['key']['id'],
                $item['key'][self::PLATFORM_ID],
                $item['state'],
                $item['totalCount'],
                $item['successCount']
            );

            $detailsArray[] = $details;
        }

        return $detailsArray;
    }

    /**
     * @param Channel $channel
     * @param bool $isNewChannel
     * @return array
     * @throws BadRequestHttpException
     */
    private function getChannelFormData(Channel $channel, bool $isNewChannel = true)
    {
        $data = [
            'name' => $channel->getAppName(),
            self::PLATFORM_ID => $channel->getPlatformId(),
            self::PARAMETERS => [
                self::SENDER_ID => $channel->getSenderId(),
                self::PUSH_TEMPLATE => $channel->getPushTemplate(),
            ],
            'tags' => [
                [
                    self::SOURCE_ID => $this->getDefaultTagSourceId()
                ]
            ]
        ];

        if (!$isNewChannel) {
            $data['id'] = $channel->getId();

            if (intval($data['id']) === 0) {
                $this->logger->error("Could not update channel without id");
                throw new BadRequestHttpException();
            }
        }

        if ($channel->isWebPush()) {
            $data[self::PARAMETERS][self::FIREBASE_MESSAGING_SENDER_ID] = $channel->getFirebaseMessagingSenderId();
            $data[self::PARAMETERS][self::FIREBASE_PROJECT_ID] = $channel->getFirebaseProjectId();
            $data[self::PARAMETERS][self::FIREBASE_API_KEY] = $channel->getFirebaseApiKey();
            $data[self::PARAMETERS][self::FIREBASE_APP_ID] = $channel->getFirebaseAppId();
            $data[self::PARAMETERS][self::FALLBACK_URL] = $channel->getFallbackUrl();
        }

        return $data;
    }

    /**
     * @param string $name
     * @param string $sourceId
     * @return bool
     */
    public function addTag(string $name, string $sourceId)
    {
        $uri = "/push-admin-api/tag/add/{$this->settings->getClientId()}";
        $tags = [
            'tags' => [
                [
                    "name" => $name,
                    self::SOURCE_ID => $sourceId
                ]
            ]
        ];

        return $this->post($uri, $tags);
    }

    /**
     * @param array $payload
     * @return bool
     */
    public function sendPushNotification(array $payload)
    {
        $data = [
            'tagSourceIds' => [$this->getDefaultTagSourceId()],
            'payload' => json_encode($payload, true)
        ];
        return $this->sendPush($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function sendPush(array $data)
    {
        $uri = "/push-api/push";
        return $this->post($uri, $data);
    }

    /**
     * @return string
     */
    public function getDefaultTagSourceId()
    {
        return "push-connector-tag-general-{$this->settings->getClientId()}";
    }

    /**
     * @return mixed|null
     */
    public function getDefaultTagId()
    {
        $item = $this->getTag(self::SOURCE_ID, $this->getDefaultTagSourceId());
        return $item['id'];
    }

    /**
     * @param string $field
     * @param string $id
     * @return mixed|null
     */
    public function getTag(string $field, string $id)
    {
        $uri = "/push-admin-api/tag/{$field}/{$id}/{$this->settings->getClientId()}";
        return $this->get($uri);
    }

    /**
     * @return array
     */
    private function getConnectedTagIds()
    {
        $connectedTagIds = [];
        array_push($connectedTagIds, $this->getDefaultTagId());
        return $connectedTagIds;
    }


    /**
     * @param string $uri
     * @return mixed|null
     */
    private function get(string $uri)
    {
        $data = null;

        try {
            $response = $this->httpClient->request('GET', $this->settings->getDomain() . $uri, [
                'auth_basic' => "{$this->settings->getUsername()}:{$this->settings->getPassword()}"
            ]);

            $json = $response->getContent(true);
            $data = json_decode($json, true);
        } catch (TransportExceptionInterface | ClientExceptionInterface
        | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->warning("GET request for $uri failed. {$e->getMessage()}");
            return null;
        }

        return $data;
    }

    /**
     * @param string $uri
     * @param array $data
     * @param bool $hasJsonBody
     * @return bool
     */
    private function post(string $uri, array $data, bool $hasJsonBody = true)
    {
        $options = [
            'auth_basic' => "{$this->settings->getUsername()}:{$this->settings->getPassword()}",
        ];

        if ($hasJsonBody) {
            $options['headers'] = ['Content-Type' => 'application/json'];
            $options['json'] = $data;
        } else {
            $formData = new FormDataPart(['data' => new JsonPart($data)]);
            $options['headers'] = $formData->getPreparedHeaders()->toArray();
            $options['body'] = $formData->bodyToIterable();
        }

        try {
            $response = $this->httpClient->request('POST', $this->settings->getDomain() . $uri, $options);
            if ($response->getStatusCode() != Response::HTTP_OK) {
                $this->logger->warning("POST request for $uri failed. Wrong http code {$response->getStatusCode()}");
                return false;
            }
        } catch (TransportExceptionInterface $e) {
            $this->logger->warning("POST request for $uri failed. {$e->getMessage()}");
            return false;
        }

        return true;
    }
}