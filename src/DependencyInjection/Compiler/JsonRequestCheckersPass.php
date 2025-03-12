<?php

declare(strict_types=1);

namespace IWF\JsonRequestCheckBundle\DependencyInjection\Compiler;

use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckersChain;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class JsonRequestCheckersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $chainDefinition = $this->findChainDefinition($container);
        $taggedCheckers = $this->collectJsonRequestCheckers($container);

        $this->appendToChain($chainDefinition, $taggedCheckers);
    }

    private function findChainDefinition(ContainerBuilder $container): Definition
    {
        if (!$container->hasDefinition(JsonRequestCheckersChain::class)) {
            // TODO - think about: dont think we need to throw an exception because its in my bundle...
            throw new LogicException(
                sprintf('No definition found for %s', JsonRequestCheckersChain::class)
            );
        }

        return $container->getDefinition(JsonRequestCheckersChain::class);
    }

    private function collectJsonRequestCheckers(ContainerBuilder $container): array
    {
        $checkers = $container->findTaggedServiceIds('iwf.jsonRequestChecker');
        if (empty($checkers)) {
            // TODO - think about: dont think we need to throw an exception because its in my bundle...
            throw new LogicException('No checkers found');
        }

        return $checkers;
    }

    private function appendToChain(Definition $chainDefinition, array $checkers): void
    {
        foreach ($checkers as $id => $tags) {
            $chainDefinition->addMethodCall('addChecker', [new Reference($id)]);
        }
    }
}
