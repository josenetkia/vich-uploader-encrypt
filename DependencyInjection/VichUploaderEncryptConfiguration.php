<?php

namespace SfCod\VichUploaderEncrypt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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
        if (Kernel::VERSION_ID >= 40300) {
            $treeBuilder = new TreeBuilder('sfcod_vich_uploader_encrypt');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sfcod_vich_uploader_encrypt');
        }

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
