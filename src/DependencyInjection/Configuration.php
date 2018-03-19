<?php

namespace VichUploaderEncrypt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vich_uploader_encrypt');

        $rootNode
            ->children()
                ->scalarNode('encryption_key')
                    ->isRequired()
                ->end()
                ->scalarNode('encryption_vi')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
