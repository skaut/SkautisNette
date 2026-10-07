<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Tracy;

use Skaut\Skautis\Wsdl\Event\RequestFailEvent;
use Skaut\Skautis\Wsdl\Event\RequestPostEvent;

/**
 * Listener that remembers every finished skautIS call for the Tracy panel.
 */
final class QueryLog
{
    /** @var list<Query> */
    private array $queries = [];

    public function __invoke(object $event): void
    {
        if ($event instanceof RequestPostEvent) {
            $this->queries[] = new Query($event->getFname(), $event->getArgs(), $event->getDuration(), $event->getResult(), null, $event->getTrace());
        } elseif ($event instanceof RequestFailEvent) {
            $this->queries[] = new Query($event->getFname(), $event->getArgs(), $event->getDuration(), null, $event->getExceptionString(), $event->getTrace());
        }
    }

    /** @return list<Query> */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Seconds spent in skautIS calls.
     */
    public function getTotalTime(): float
    {
        return array_sum(array_map(static fn (Query $query): float => $query->duration, $this->queries));
    }
}
