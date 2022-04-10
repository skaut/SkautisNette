<?php
declare(strict_types=1);

namespace Skautis\Nette\Tracy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Skaut\Skautis\Wsdl\Event\RequestFailEvent;
use Skaut\Skautis\Wsdl\Event\RequestPostEvent;
use Skaut\Skautis\Wsdl\Event\RequestPreEvent;
use stdClass;


/**
 * Trida pro logovani dotazu.
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var SkautisQuery[] */
    private $requests = [];

    /** @var SkautisQuery[] */
    private $queries = [];

    /**
     * @param RequestPreEvent|RequestPostEvent|RequestFailEvent $event
     */
    public function dispatch($event): void
    {
        $requestId = new stdClass();
        $requestId->fname = $event->getFname();
        $requestId->args = $event->getArgs();
        $requestHash = md5(serialize($requestId));

        switch (true) {
            case $event instanceof RequestPreEvent:
                $this->requests[$requestHash] = SkautisQuery::createFromPreEvent($event);
                break;
            case $event instanceof RequestPostEvent:
                if (key_exists($requestHash, $this->requests)) {
                    $this->queries[] = SkautisQuery::updateFromPostEvent($this->requests[$requestHash], $event);
                } else {
                    $this->queries[] = SkautisQuery::createFromPostEvent($event); // should not happen
                }
                break;
            case $event instanceof RequestFailEvent:
                if (key_exists($requestHash, $this->requests)) {
                    $this->queries[] = SkautisQuery::updateFromFailEvent($this->requests[$requestHash], $event);
                } else {
                    $this->queries[] = SkautisQuery::createFromFailEvent($event); // should not happen
                }
                break;
        }
    }

    /** @return SkautisQuery[] */
    public function getQueries(): array
    {
        return $this->queries;
    }
}