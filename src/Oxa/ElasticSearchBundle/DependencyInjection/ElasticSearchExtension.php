<?php

namespace Oxa\ElasticSearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class ElasticSearchExtension
 * @package Oxa\ElasticSearchBundle\DependencyInjection
 */
class ElasticSearchExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('elastic_search.document_index', $config['document_index']);
        $container->setParameter('elastic_search.indexing_page', $config['indexing_page']);
        $container->setParameter('elastic_search.host', $config['host']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
