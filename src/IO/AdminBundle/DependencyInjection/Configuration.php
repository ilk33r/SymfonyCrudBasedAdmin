<?php

namespace IO\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('io_admin');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

		$rootNode
			->children()
				->scalarNode('admin_prefix')->isRequired()->cannotBeEmpty()->end()
				->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
				->arrayNode('admin_menu')
					->canBeUnset()
					->prototype('array')
						->addDefaultsIfNotSet()
						->children()
							->scalarNode('category')->defaultValue('')->end()
							->scalarNode('path')->defaultValue('')->end()
							->booleanNode('hasAddPermission')->defaultValue(true)->end()
						->end()
					->end()
				->end()
			->end();

        return $treeBuilder;
    }
}
