<?php

namespace SfCod\VichUploaderEncrypt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class VichUploaderEncryptConfiguration
 * @author Virchenko Maksim <muslim1992@gmail.com>
 * @package SfCod\VichUploaderEncrypt\DependencyInjection
 */
class VichUploaderEncryptConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sfcod_vich_uploader_encrypt');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('encryption_key')
                    ->isRequired()
                ->end()
                ->scalarNode('encryption_iv')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
