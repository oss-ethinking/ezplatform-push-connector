<?php

declare(strict_types=1);

namespace Ethinking\PushConnector\EzPlatform\UI\Menu\EventSubscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ethinking\PushConnectorBundle\Service\PushApiService;

/**
 * Class PushConnectorMenuSubscriber
 * @package Ethinking\PushConnector\EzPlatform\UI\Menu\EventSubscriber
 */
class PushConnectorMenuSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    const PUSH_MAIN_MENU_ITEM = 'push__main__menu__item';
    const PUSH_MAIN_SETTINGS_ITEM = 'push__general__settings__item';
    const PUSH_CHANNEL_ITEM = 'push__delivery__channel';
    const PUSH_ARCHIVE_ITEM = 'push__delivery__archive';
    const EXTRAS = 'extras';
    const TRANSLATION_DOMAIN = 'translation_domain';
    const ROUTE = 'route';


    /**
     * @todo add permissions
     * @var \eZ\Publish\API\Repository\PermissionResolver
     */
    private $permissionResolver;

    /**
     * @var PushApiService
     */
    private $pushApiService;

    /**
     * PushConnectorMenuSubscriber constructor.
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     */
    public function __construct(PermissionResolver $permissionResolver, PushApiService $pushApiService)
    {
        $this->permissionResolver = $permissionResolver;
        $this->pushApiService = $pushApiService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => [
                ['onMainMenuConfigure']
            ],
        ];
    }

    /**
     * @param \EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent $event
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function onMainMenuConfigure(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();


        if ($this->permissionResolver->hasAccess('push', 'main_menu')) {
            $root->addChild(
                self::PUSH_MAIN_MENU_ITEM,
                [
                    self::EXTRAS => [self::TRANSLATION_DOMAIN => 'menu'],
                ]
            );
        }

        if ($this->permissionResolver->hasAccess('push', 'settings')) {
            $root[self::PUSH_MAIN_MENU_ITEM]->addChild(
                self::PUSH_MAIN_SETTINGS_ITEM,
                [
                    self::ROUTE => 'ezplatform.push.main_settings.view',
                    self::EXTRAS => [self::TRANSLATION_DOMAIN => 'menu'],
                ]
            );
        }
        if (!empty($this->pushApiService->getSettings())) {
            $root[self::PUSH_MAIN_MENU_ITEM]->addChild(
                self::PUSH_CHANNEL_ITEM,
                [
                    self::ROUTE => 'ezplatform.push.channel.view',
                    self::EXTRAS => [self::TRANSLATION_DOMAIN => 'menu'],
                ]
            );

            $root[self::PUSH_MAIN_MENU_ITEM]->addChild(
                self::PUSH_ARCHIVE_ITEM,
                [
                    self::ROUTE => 'ezplatform.push.archive.view',
                    self::EXTRAS => [self::TRANSLATION_DOMAIN => 'menu'],
                ]
            );
        }


    }

    /**
     * @return array
     */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::PUSH_MAIN_MENU_ITEM, 'menu'))->setDesc('Push'),
            (new Message(self::PUSH_MAIN_SETTINGS_ITEM, 'menu'))->setDesc('Main settings'),
            (new Message(self::PUSH_CHANNEL_ITEM, 'menu'))->setDesc('Push Channels'),
            (new Message(self::PUSH_ARCHIVE_ITEM, 'menu'))->setDesc('Push Archive'),
        ];
    }
}