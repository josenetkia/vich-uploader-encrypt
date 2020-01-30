<?php

namespace SfCod\VichUploaderEncrypt\DependencyInjection;

use SfCod\VichUploaderEncrypt\Command\EncryptFileCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use SfCod\VichUploaderEncrypt\Crypt\Encryption;

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
        $configuration = new VichUploaderEncryptConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        if (empty($config['encryption_key']) || empty($config['encryption_iv'])) {
            throw new \RuntimeException('You must provide "encryption_key" and "encryption_iv" for VichUploaderEncryptBundle');
        }

        $encryption = new Definition(Encryption::class);
        $encryption->setArguments([
            $config['encryption_key'],
            $config['encryption_iv'],
        ]);

        $command = new Definition(EncryptFileCommand::class);
        $command
            ->setArguments([
                new Reference(Encryption::class),
                $container->getParameter('vich_uploader.mappings'),
            ])
            ->addTag('console.command');

        $container->addDefinitions([
            Encryption::class => $encryption,
            EncryptFileCommand::class => $command,
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
