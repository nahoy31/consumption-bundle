<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ConsumptionExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('commands.yml');
        $loader->load('orm.yml');
        $loader->load('services.yml');

        if (true === boolval($config['enabled_limit'])) {
            $loader->load('limit.yml');
        }

        $this->configureClass($config, $container);

        if (null !== $config['cache']) {
            $cache = new Reference($config['cache']);
        } else {
            $cache = new Definition(FilesystemAdapter::class, ['nahoy_consumption', 0, $container->getParameter('kernel.cache_dir')]);
        }

        $container->setParameter('nahoy_api_platform_consumption.enabled_limit',        boolval($config['enabled_limit']));
        $container->setParameter('nahoy_api_platform_consumption.api_pattern',          $config['api_pattern']);
        $container->setParameter('nahoy_api_platform_consumption.exception',            $config['exception']);
        $container->setParameter('nahoy_api_platform_consumption.getter.user_id',       $config['getter']['user_id']);
        $container->setParameter('nahoy_api_platform_consumption.getter.user_username', $config['getter']['user_username']);
        $container->setParameter('nahoy_api_platform_consumption.getter.user_limit',    $config['getter']['user_limit']);
        $container->setParameter('nahoy_api_platform_consumption.routes_with_limit',    $config['routes_with_limit']);

        $container->getDefinition('nahoy_api_platform_consumption.service.cache')
            ->replaceArgument(0, $cache)
        ;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        $container->setParameter('nahoy_api_platform_consumption.class.consumption', $config['class']['consumption']);
        $container->setParameter('nahoy_api_platform_consumption.class.user',        $config['class']['user']);
    }
}
