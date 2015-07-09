<?php

namespace IO\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IOAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

		$ioAdminService = $container->register('io_admin', 'IO\AdminBundle\Extension\IOAdmin')->addArgument($config);
		$container->register('io_admin.twig_extension', 'IO\AdminBundle\Extension\IOAdminTwigExtension')->addArgument($ioAdminService)->addTag('twig.extension');

		$container->setParameter('io_admin.user_class', $config['user_class']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
