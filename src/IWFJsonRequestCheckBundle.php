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

namespace IWF\JsonRequestCheckBundle;

use IWF\JsonRequestCheckBundle\DependencyInjection\Compiler\JsonRequestCheckersPass;
use IWF\JsonRequestCheckBundle\DependencyInjection\Compiler\MaxContentLengthValuePass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IWFJsonRequestCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->addCompilerPasses($container, [
            new MaxContentLengthValuePass(),
            new JsonRequestCheckersPass(),
        ]);
    }

    /**
     * @param ContainerBuilder $container
     * @param array<CompilerPassInterface> $compilerPasses
     * @return void
     */
    public function addCompilerPasses(ContainerBuilder $container, array $compilerPasses): void
    {
        foreach ($compilerPasses as $compilerPass) {
            $container->addCompilerPass($compilerPass);
        }
    }
}