<?php
/*
 * This file is part of the IWFJsonRequestCheckBundle package.
 *
 * (c) IWF AG / IWF Web Solutions <info@iwf.ch>
 * Author: Nick Steinwand <n.steinwand@iwf.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace IWF\JsonRequestCheckBundle\DependencyInjection\Compiler;

use IWF\JsonRequestCheckBundle\Attribute\JsonRequestCheck;
use IWF\JsonRequestCheckBundle\EventSubscriber\JsonRequestCheckSubscriber;
use IWF\JsonRequestCheckBundle\Provider\MaxContentLengthValueProvider;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * CompilerPass to collect all controllers with JsonRequestCheck attributes.
 *
 * Scans all registered controllers and collects JsonRequestCheck attribute settings
 * to make them available for runtime processing.
 */
final class MaxContentLengthValuePass implements CompilerPassInterface
{
    private const CONTROLLER_TAG = 'controller.service_arguments';

    /**
     * Processes the compiler pass to collect all JsonRequestCheck attribute values
     * and provide them to the JsonRequestCheckMaxContentLengthValueProvider.
     *
     * @throws LogicException If the JsonRequestCheckSubscriber is not registered
     * @throws ServiceNotFoundException If a required service cannot be found
     */
    public function process(ContainerBuilder $container): void
    {
        $this->validateRequiredServices($container);

        $jsonRequestCheckClassMap = $this->collectJsonRequestCheckAttributes($container);

        $this->registerClassMap($container, $jsonRequestCheckClassMap);
    }

    /**
     * Checks if all required services are registered in the container.
     *
     * @throws LogicException If a required service is missing
     */
    private function validateRequiredServices(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(JsonRequestCheckSubscriber::class)) {
            throw new LogicException(
                sprintf('No definition found for %s', JsonRequestCheckSubscriber::class)
            );
        }
    }

    /**
     * Collects all JsonRequestCheck attribute settings from registered controllers.
     *
     * @return array<string, int> Map of controller class::method to maximum content length value
     * @throws ServiceNotFoundException If a service cannot be found
     */
    private function collectJsonRequestCheckAttributes(ContainerBuilder $container): array
    {
        $jsonRequestCheckClassMap = [];
        $controllerDefinitions = $this->getControllerDefinitions($container);

        foreach ($controllerDefinitions as $serviceDefinition) {
            $className = $serviceDefinition->getClass();
            $reflClass = $container->getReflectionClass($className);

            if (!$reflClass instanceof ReflectionClass) {
                continue;
            }

            $this->processClassMethods($reflClass, $className, $jsonRequestCheckClassMap);
        }

        return $jsonRequestCheckClassMap;
    }

    /**
     * Returns all controller service definitions.
     *
     * @return Definition[] Array of controller definitions
     */
    private function getControllerDefinitions(ContainerBuilder $container): array
    {
        $controllerIds = array_keys($container->findTaggedServiceIds(self::CONTROLLER_TAG));

        return array_map(
            fn(string $id) => $container->getDefinition($id),
            $controllerIds
        );
    }

    /**
     * Processes the methods of a controller class to find JsonRequestCheck attributes.
     *
     * @param ReflectionClass $reflClass The ReflectionClass instance of the controller class
     * @param string $className The full class name of the controller
     * @param array<string, int> $jsonRequestCheckClassMap The map of controller to content length
     * @throws ServiceNotFoundException If a service cannot be found
     */
    private function processClassMethods(
        ReflectionClass $reflClass,
        string $className,
        array &$jsonRequestCheckClassMap
    ): void {
        $publicNonStaticMethods = $reflClass->getMethods(ReflectionMethod::IS_PUBLIC | ~ReflectionMethod::IS_STATIC);

        foreach ($publicNonStaticMethods as $reflMethod) {
            $attributes = $reflMethod->getAttributes(JsonRequestCheck::class);

            if (empty($attributes)) {
                continue;
            }

            $this->addMethodConfigToClassMap($attributes[0], $className, $reflMethod->getName(), $jsonRequestCheckClassMap);
        }
    }

    /**
     * Adds the method configuration to the class map.
     *
     * @param \ReflectionAttribute $attribute The JsonRequestCheck attribute
     * @param string $className The class name of the controller
     * @param string $methodName The method name
     * @param array<string, int> $jsonRequestCheckClassMap The map of controller to content length
     * @throws ServiceNotFoundException If a service cannot be found
     */
    private function addMethodConfigToClassMap(
        \ReflectionAttribute $attribute,
        string $className,
        string $methodName,
        array &$jsonRequestCheckClassMap
    ): void {
        try {
            $classMapKey = sprintf('%s::%s', $className, $methodName);
            $attributeInstance = $attribute->newInstance();

            if ($attributeInstance instanceof JsonRequestCheck) {
                $jsonRequestCheckClassMap[$classMapKey] = $attributeInstance->getMaxJsonContentSize();
            }
        } catch (ServiceNotFoundException $e) {
            $this->throwServiceNotFoundException($e, $classMapKey);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf('Error processing JsonRequestCheck attribute for %s: %s', $classMapKey, $e->getMessage()),
                0,
                $e
            );
        }
    }

    /**
     * Registers the class map in the container.
     */
    private function registerClassMap(ContainerBuilder $container, array $jsonRequestCheckClassMap): void
    {
        $container->getDefinition(MaxContentLengthValueProvider::class)
            ->setArgument('$jsonRequestCheckClassMap', $jsonRequestCheckClassMap);
    }

    /**
     * Throws an enhanced ServiceNotFoundException with additional context.
     *
     * @throws ServiceNotFoundException
     */
    private function throwServiceNotFoundException(ServiceNotFoundException|\Exception $e, string $calledFrom): void
    {
        throw new ServiceNotFoundException(
            id: $e->getId(),
            msg: $e->getMessage() . ' ' . sprintf('Called from %s ', $calledFrom),
        );
    }
}