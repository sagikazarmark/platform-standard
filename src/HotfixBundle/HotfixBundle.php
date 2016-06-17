<?php

namespace HotfixBundle;

use HotfixBundle\DependencyInjection\Compiler\ViewListenerPriorityPass;
use HotfixBundle\DependencyInjection\Compiler\WssePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HotfixBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ViewListenerPriorityPass());
        $container->addCompilerPass(new WssePass());
    }
}
