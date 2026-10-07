<?php

declare(strict_types=1);

use Psr\EventDispatcher\StoppableEventInterface;
use Skaut\SkautisNette\EventDispatcher;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

test('every listener receives the event and the event is returned', function (): void {
    $dispatcher = new EventDispatcher();
    $received = [];
    $dispatcher->addListener(function (object $event) use (&$received): void {
        $received[] = 'a';
    });
    $dispatcher->addListener(function (object $event) use (&$received): void {
        $received[] = 'b';
    });

    $event = new stdClass();

    Assert::same($event, $dispatcher->dispatch($event));
    Assert::same(['a', 'b'], $received);
});

test('a stopped event is not passed to the remaining listeners', function (): void {
    $event = new class implements StoppableEventInterface {
        public bool $stopped = false;

        public function isPropagationStopped(): bool
        {
            return $this->stopped;
        }
    };

    $dispatcher = new EventDispatcher();
    $received = [];
    $dispatcher->addListener(function (object $event) use (&$received): void {
        $received[] = 'first';
        $event->stopped = true;
    });
    $dispatcher->addListener(function (object $event) use (&$received): void {
        $received[] = 'second';
    });

    $dispatcher->dispatch($event);

    Assert::same(['first'], $received);
});
