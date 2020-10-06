<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\EzPlatform\UI\Menu\EventSubscriber;

use Ethinking\EthinkingPushApiBundle\Service\PushApiInstance;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;
use Ethinking\PushConnector\Connector\Services\ConfigurationDefinitionService;
use Ethinking\PushConnectorBundle\Service\PushService;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ContentSidebarRightSubscriber
 * @package Ethinking\PushConnector\EzPlatform\UI\Menu\EventSubscriber
 */
final class ContentSidebarRightSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    private const PUSH_CONTENT_SIDEBAR_RIGHT_MENU_ITEM = 'push__content__sidebar__right__menu__item';

    /**
     * @var PermissionResolver
     */
    private $permissionResolver;

    /**
     * @var ConfigurationDefinitionService
     */
    private $configurationDefinitionService;

    /**
     * @var PushApiInstance
     */
    private $pushApiService;

    /**
     * @param PermissionResolver $permissionResolver
     * @param ConfigurationDefinitionService $configurationDefinitionService
     * @param PushService $pushService
     */
    public function __construct(
        PermissionResolver $permissionResolver,
        ConfigurationDefinitionService $configurationDefinitionService,
        PushService $pushService
    )
    {
        $this->permissionResolver = $permissionResolver;
        $this->configurationDefinitionService = $configurationDefinitionService;
        $this->pushApiService = $pushService->getPushApiService();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::CONTENT_SIDEBAR_RIGHT => [
                ['onContentSidebarRightMenuConfigure']
            ],
        ];
    }

    /**
     * @param ConfigureMenuEvent $event
     * @throws InvalidArgumentException
     */
    public function onContentSidebarRightMenuConfigure(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();
        $options = $event->getOptions();

        if (!$this->configurationDefinitionService->getSupportedFields($options['content'])) {
            return;
        }

        if ($this->permissionResolver->hasAccess('push', 'content_create_push') && !empty($this->pushApiService)) {
            $root->addChild(
                self::PUSH_CONTENT_SIDEBAR_RIGHT_MENU_ITEM,
                [
                    'route' => 'ezplatform.push_content',
                    'routeParameters' => ['locationId' => $options['location']->id],
                    'extras' => [
                        'translation_domain' => 'menu',
                        'icon_path' => '/bundles/ezplatformpushconnector/img/push-delivery-icon.svg#push',
                        'icon_class' => 'ez-push-show',
                    ]
                ]
            );
        }
    }

    /** @return array */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::PUSH_CONTENT_SIDEBAR_RIGHT_MENU_ITEM, 'menu'))->setDesc('Push'),
        ];
    }
}