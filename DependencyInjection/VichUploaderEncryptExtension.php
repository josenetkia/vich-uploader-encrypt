<?php

namespace SfCod\VichUploaderEncrypt\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use VichUploaderEncrypt\Crypt\Encryption;

/**
 * Class VichUploaderEncryptExtension
 * @package VichUploaderEncrypt\DependencyInjection
 */
class VichUploaderEncryptExtension extends Extension
{
    /**
     * Load configuration
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $encryption = new Definition(Encryption::class);
        $encryption->setArguments([
            $config['encryption_key'],
            $config['encryption_vi'],
        ]);

        $container->addDefinitions([
            Encryption::class => $encryption,
        ]);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * Get bundle alias
     *
     * @return string
     */
    public function getAlias()
    {
        return 'sfcod_vich_uploader_encrypt';
    }
}
