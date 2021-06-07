<?php

namespace AcMarche\Bottin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AcMarcheBottinExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $yamlFileLoader = new Loader\YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));
        $yamlFileLoader->load('services.yaml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        // get all bundles
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach (array_keys($containerBuilder->getExtensions()) as $name) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfig($containerBuilder, 'doctrine');
                        break;
                    case 'twig':
                        $this->loadConfig($containerBuilder, 'twig', true);
                        break;
                    case 'liip_imagine':
                        $this->loadConfig($containerBuilder, 'liip_imagine');
                        break;
                    case 'framework':
                        $this->loadConfig($containerBuilder, 'security');
                        break;
                    case 'vich_uploader':
                        $this->loadConfig($containerBuilder, 'vich_uploader');
                        break;
                    case 'api_platform':
                        $this->loadConfig($containerBuilder, 'api_platform');
                        break;
                }
            }
        }
    }

    protected function loadConfig(ContainerBuilder $containerBuilder, string $name, bool $php = false): void
    {
        if ($php) {
            $configs = $this->loadPhpFile($containerBuilder);
            //  $configs->load($name.'.php');

            return;
        }
        $configs = $this->loadYamlFile($containerBuilder);
        $configs->load($name.'.yaml');
    }

    protected function loadYamlFile(ContainerBuilder $containerBuilder): Loader\YamlFileLoader
    {
        return new Loader\YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages')
        );
    }

    private function loadPhpFile(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages')
        );
    }
}
