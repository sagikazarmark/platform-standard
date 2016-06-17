<?php

namespace HotfixBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class WssePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('escape_wsse_authentication.provider')) {
            $definition = $container->getDefinition('escape_wsse_authentication.provider');
            $definition->addMethodCall('setClockSkew', ['%hotfix.wsse.clock_skew%']);
        }
    }
}
