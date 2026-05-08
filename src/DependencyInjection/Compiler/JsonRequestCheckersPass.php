<?php

declare(strict_types=1);

/**
 * JSON Request Check Bundle
 *
 * @package   JsonRequestCheckBundle
 * @author    IWF Web Solutions <web-solutions@iwf.ch>
 * @copyright Copyright (c) 2025-2026 IWF Web Solutions <web-solutions@iwf.ch>
 * @license   https://github.com/iwf-web/json-request-check-bundle/blob/main/LICENSE.txt MIT License
 * @link      https://github.com/iwf-web/json-request-check-bundle
 */

namespace IWFWeb\JsonRequestCheckBundle\DependencyInjection\Compiler;

use IWFWeb\JsonRequestCheckBundle\Check\JsonRequestCheckersChain;
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
            throw new \LogicException(
                \sprintf('No definition found for %s', JsonRequestCheckersChain::class),
            );
        }

        return $container->getDefinition(JsonRequestCheckersChain::class);
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    private function collectJsonRequestCheckers(ContainerBuilder $container): array
    {
        $checkers = $container->findTaggedServiceIds('iwf.jsonRequestChecker');
        if (empty($checkers)) {
            throw new \LogicException('No checkers found');
        }

        return $checkers;
    }

    /**
     * @param array<string, list<array<string, mixed>>> $checkers
     */
    private function appendToChain(Definition $chainDefinition, array $checkers): void
    {
        foreach ($checkers as $id => $tags) {
            $chainDefinition->addMethodCall('addChecker', [new Reference($id)]);
        }
    }
}
