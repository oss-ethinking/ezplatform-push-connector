<?php

namespace Ethinking\PushConnector\EzPlatform\UI\Menu\EventSubscriber;

use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ethinking\PushConnector\Connector\Services\ConfigurationDefinitionService;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PushContentViewTabSubscriber extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{

    /** @var PermissionResolver */
    private $permissionResolver;

    /** @var ConfigResolverInterface */
    private $configResolver;
    /**
     * @var ConfigurationDefinitionService
     */
    private $configurationDefinitionService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ConfigurationDefinitionService $configurationDefinitionService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->configurationDefinitionService = $configurationDefinitionService;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'push_tab';
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1000;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        /** @Desc("Push Info") */
        return $this->translator->trans('push.content.view.menu.item', [], 'menu');
    }

    /**
     * @param array $parameters
     * @return bool
     * @throws BadStateException
     * @throws InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        return !(false === $this->permissionResolver->canUser('push', 'content_tab_view', $parameters['content']) || !$this->configurationDefinitionService->getSupportedFields($parameters['content']));

    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return '@ezdesign/content/tab/push_content_view_tab.html.twig';
    }

    /**
     * @param array $contextParameters
     * @return array
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        return [
            'data' => 'value'
        ];
    }
}