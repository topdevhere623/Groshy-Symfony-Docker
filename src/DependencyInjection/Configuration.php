<?php

declare(strict_types=1);

namespace Groshy\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('groshy');
        $this->addResourceSection($treeBuilder->getRootNode());
        $this->addAssets($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $node)
    {
        $resources = [
            'tag_group',
            'tag',
            'sponsor',
            'asset',
            'asset_investment',
            'asset_cash',
            'asset_type',
            'position',
            'position_investment',
            'position_cash',
            'position_value',
            'transaction',
            'position_credit_card',
            'asset_credit_card',
            'tag_group',
            'institution',
        ];
        $builder = $node->children()->arrayNode('resources')->addDefaultsIfNotSet()->children();
        foreach ($resources as $resName) {
            $this->addResourceNode($builder, $resName);
        }
        $builder->end()->end()->end();
    }

    private function addResourceNode(NodeBuilder $node, string $name): void
    {
        $node->arrayNode($name)
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('model')->cannotBeEmpty()->end()
                        ->scalarNode('manager')->end()
                        ->scalarNode('repository')->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    private function addAssets(ArrayNodeDefinition $node): void
    {
        $node->children()
            ->arrayNode('assets')
                ->children()
                    ->arrayNode('configs')
                        ->children()
                            ->arrayNode('private_equity')
                                ->children()
                                    ->scalarNode('type_right_panel_template')->end()
                                    ->scalarNode('type_positions_template')->end()
                                    ->scalarNode('position_right_panel_template')->end()
                                    ->scalarNode('asset_class')->end()
                                    ->scalarNode('position_class')->end()
                                ->end()
                            ->end()
                            ->arrayNode('cash')
                                ->children()
                                    ->scalarNode('type_right_panel_template')->end()
                                    ->scalarNode('type_positions_template')->end()
                                    ->scalarNode('position_right_panel_template')->end()
                                    ->scalarNode('asset_class')->end()
                                    ->scalarNode('position_class')->end()
                                ->end()
                            ->end()
                            ->arrayNode('credit_card')
                                ->children()
                                    ->scalarNode('type_right_panel_template')->end()
                                    ->scalarNode('type_positions_template')->end()
                                    ->scalarNode('position_right_panel_template')->end()
                                    ->scalarNode('asset_class')->end()
                                    ->scalarNode('position_class')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('types')
                        ->normalizeKeys(false)
                        ->useAttributeAsKey('name', false)
                        ->prototype('scalar')
                    ->end()
                ->end()
            ->end();
    }
}
