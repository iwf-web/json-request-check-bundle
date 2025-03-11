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

namespace IWF\JsonRequestCheckBundle\DependencyInjection;

use IWF\JsonRequestCheckBundle\Attribute\JsonRequestChecker;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class IWFJsonRequestCheckExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('iwf_json_request_check.default_max_content_length', $config['default_max_content_length']);

        $container->registerAttributeForAutoconfiguration(
            JsonRequestChecker::class,
            static function (ChildDefinition $definition, JsonRequestChecker $attribute, \ReflectionClass $reflector): void {
                $definition->addTag('iwf.jsonRequestChecker', ['token' => $attribute->getPriority()]);
            }
        );
    }
}