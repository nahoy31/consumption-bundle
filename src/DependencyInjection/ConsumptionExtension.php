<?php

namespace Nahoy\ApiPlatform\ConsumptionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
        $loader->load('orm.yml');
        $loader->load('services.yml');

        $this->configureClass($config, $container);

        $container->setParameter('nahoy_api_platform_consumption.api_pattern',          $config['api_pattern']);
        $container->setParameter('nahoy_api_platform_consumption.getter.user_id',       $config['getter']['user_id']);
        $container->setParameter('nahoy_api_platform_consumption.getter.user_username', $config['getter']['user_username']);
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
