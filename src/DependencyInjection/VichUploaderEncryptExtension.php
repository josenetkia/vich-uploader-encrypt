<?php

namespace  VichUploaderEncryp\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class VichUploaderEncryptExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('vich_uploader_encryption.key', $config['encryption_key']);
        $container->setParameter('vich_uploader_encryption.vi', $config['encryption_vi']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'vich_uploader_encryp';
    }
}
