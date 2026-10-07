<?php

declare(strict_types=1);

namespace Skaut\SkautisNette;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * The single PSR-14 dispatcher the library accepts; hands every event to all registered listeners
 * (the Tracy panel, application logging, ...).
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /** @var list<callable(object): void> */
    private array $listeners = [];

    /**
     * @param callable(object): void $listener receives Skaut\Skautis\Wsdl\Event\Request*Event instances
     */
    public function addListener(callable $listener): void
    {
        $this->listeners[] = $listener;
    }

    public function dispatch(object $event): object
    {
        foreach ($this->listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}
