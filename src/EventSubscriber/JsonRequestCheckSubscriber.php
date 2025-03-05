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

use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckersRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class JsonRequestCheckSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JsonRequestCheckersRepository $checksRepository,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'checkRequest',
        ];
    }

    public function checkRequest(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->checksRepository->checkEvent($event);

    }
}
