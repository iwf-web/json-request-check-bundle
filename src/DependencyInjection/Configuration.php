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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('iwf_json_request_check');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->integerNode('default_max_content_length')
            ->info('Default value for the maximum JSON content size limit in bytes')
            ->defaultValue(10240) // 10KB as default
            ->end()
            ->end();

        return $treeBuilder;
    }
}