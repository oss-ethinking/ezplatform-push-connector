<?php

namespace Ethinking\PushConnectorBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class PushConfigParser extends AbstractParser
{
    private const ROOT_NODE_KEY = 'push_config';
    private const CONTENT_TYPES_MAP_NODE_KEY = 'content_types_map';
    private const CONTENT_TYPES_PARAMETER_NAME = 'push_config.content_types_map';

    /**
     * configuration example for all backend
     * admin_group:
     *      push_config:
     *          content_types_map:
     *              article:
     *                  webpush:
     *                      fields:
     *                          title: 'title'
     *                          message: 'message'
     *                          icon: 'icon'
     *                      enabled: true
     *                  whatsapp:
     *                      fields:
     *                          body: 'title'
     *                          image: 'image'
     *                      enabled: false
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode(self::ROOT_NODE_KEY)
                ->info('Push configuration')
                    ->children()
                        ->arrayNode(self::CONTENT_TYPES_MAP_NODE_KEY)
                            ->useAttributeAsKey('identifier')
                            ->arrayPrototype()
                                ->useAttributeAsKey('channel')
                                ->arrayPrototype()
                                    ->children()
                                        ->arrayNode('fields')
                                            ->children()
                                                ->scalarNode('title')->isRequired()->end()
                                                ->scalarNode('message')->isRequired()->end()
                                                ->scalarNode('icon')->end()
                                            ->end()
                                        ->end()
                                        ->booleanNode('enabled')->defaultFalse()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end();
    }

    /**
     * Does semantic config to internal container parameters mapping for $currentScope.
     *
     * This method is called by the `ConfigurationProcessor`, for each available scopes (e.g. SiteAccess, SiteAccess groups or "global").
     *
     * @param array $scopeSettings Parsed semantic configuration for current scope.
     *                             It is passed by reference, making it possible to alter it for usage after `mapConfig()` has run.
     * @param string $currentScope
     * @param ContextualizerInterface $contextualizer
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings[self::ROOT_NODE_KEY])) {
            return;
        }

        if (isset($scopeSettings[self::ROOT_NODE_KEY][self::CONTENT_TYPES_MAP_NODE_KEY])) {
            $scopeSettings[self::CONTENT_TYPES_PARAMETER_NAME] =
                $scopeSettings[self::ROOT_NODE_KEY][self::CONTENT_TYPES_MAP_NODE_KEY];
            unset($scopeSettings[self::ROOT_NODE_KEY][self::CONTENT_TYPES_MAP_NODE_KEY]);
        }

        $contextualizer->setContextualParameter(
            self::CONTENT_TYPES_PARAMETER_NAME,
            $currentScope,
            $scopeSettings[self::CONTENT_TYPES_MAP_NODE_KEY] ?? []
        );
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->mapConfigArray(self::CONTENT_TYPES_PARAMETER_NAME, $config);
    }
}
