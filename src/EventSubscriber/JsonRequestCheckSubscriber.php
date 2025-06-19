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

namespace IWF\JsonRequestCheckBundle\EventSubscriber;

use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckersChain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class JsonRequestCheckSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JsonRequestCheckersChain $checkersChain,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'checkEvent',
        ];
    }

    /**
     * @throws HttpExceptionInterface
     */
    public function checkEvent(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $this->checkersChain->checkEvent($event);
        } catch (HttpExceptionInterface $e) {
            if (true) {
                throw $e;
            }
        }
    }
}
