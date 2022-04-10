<?php
declare(strict_types=1);

namespace Skautis\Nette\Tracy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Skaut\Skautis\Wsdl\Event\RequestFailEvent;
use Skaut\Skautis\Wsdl\Event\RequestPostEvent;
use Skaut\Skautis\Wsdl\Event\RequestPreEvent;


/**
 * Trida pro logovani dotazu.
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var SkautisQuery[] */
    private $queries = [];

    /**
     * @param RequestPreEvent|RequestPostEvent|RequestFailEvent $event
     */
    public function dispatch($event): void
    {
        switch (true) {
            case $event instanceof RequestPreEvent:
                // no-op
                break;
            case $event instanceof RequestPostEvent:
                $this->queries[] = SkautisQuery::createFromPostEvent($event);
                break;
            case $event instanceof RequestFailEvent:
                $this->queries[] = SkautisQuery::createFromFailEvent($event);
                break;
        }
    }

    /** @return SkautisQuery[] */
    public function getQueries(): array
    {
        return $this->queries;
    }
}