<?php
declare(strict_types=1);

namespace Skautis\Nette\Tracy;

use Skaut\Skautis\Wsdl\Event\RequestFailEvent;
use Skaut\Skautis\Wsdl\Event\RequestPostEvent;
use Skaut\Skautis\Wsdl\Event\RequestPreEvent;


/**
 * Objekt s informacemi o dotazu.
 */
class SkautisQuery
{
    /**
     * Nazev funkce volane pomoci SOAP requestu.
     * @var string
     */
    public $fname;

    /**
     * Parametry SOAP requestu na server.
     * @var array<int, mixed>
     */
    public $args;

    /**
     * Zasobnik volanych funkci.
     * @var array<int, array<string, mixed>>
     */
    public $trace;

    /**
     * Doba trvani pozadvku.
     * @var float
     */
    public $time;

    /**
     * SOAP response.
     * @var mixed
     */
    public $result;

    private function __construct($fname, $args, $trace, $time, $result)
    {
        $this->fname = $fname;
        $this->args = $args;
        $this->trace = $trace;
        $this->time = $time;
        $this->result = $result;
    }

    public static function createFromPreEvent(RequestPreEvent $event): SkautisQuery {
        return new SkautisQuery($event->getFname(), $event->getArgs(), $event->getTrace(), 0, null);
    }

    public static function createFromPostEvent(RequestPostEvent $event): SkautisQuery {
        return new SkautisQuery($event->getFname(), $event->getArgs(), [], $event->getDuration(), $event->getResult());
    }

    public static function createFromFailEvent(RequestFailEvent $event): SkautisQuery {
        return new SkautisQuery($event->getFname(), $event->getArgs(), [], $event->getDuration(), null);
    }

    public static function updateFromPostEvent(SkautisQuery $query, RequestPostEvent $event): SkautisQuery {
        $query->time = $event->getDuration();
        $query->result = $event->getResult();
        return $query;
    }

    public static function updateFromFailEvent(SkautisQuery $query, RequestFailEvent $event): SkautisQuery {
        $query->time = $event->getDuration();
        return $query;
    }
}