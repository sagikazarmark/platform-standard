<?php

namespace HotfixBundle\DependencyInjection;

use HotfixBundle\EventListener\ImportExportConfigListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HotfixExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('hotfix.wsse.clock_skew', $config['wsse']['clock_skew']);

        $this->loadImportExportConfig($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadImportExportConfig(array $config, ContainerBuilder $container)
    {
        $processors = $config['import_export']['processors'];
        $config = [];

        foreach ($processors as $alias => $processor) {
            $config[$alias] = $processor['config'];
        }

        $definition = new Definition(ImportExportConfigListener::class, [$config]);

        $definition->addTag(
            'kernel.event_listener',
            [
                'event' => 'akeneo_batch.before_step_execution',
                'method' => 'onStepExecution',
            ]
        );

        $container->setDefinition('hotfix.listener.import_export_config', $definition);
    }
}
