<?php

declare(strict_types=1);

use Skaut\Skautis\InvalidArgumentException;
use Skaut\Skautis\Skautis;
use Skaut\Skautis\Wsdl\Event\RequestPostEvent;
use Skaut\Skautis\Wsdl\WebServiceFactory;
use Skaut\SkautisNette\EventDispatcher;
use Skaut\SkautisNette\Tracy\Panel;
use Skaut\SkautisNette\Tracy\QueryLog;
use Tester\Assert;
use Tracy\Bar;

require __DIR__.'/../bootstrap.php';

$container = createContainer(['applicationId' => 'test', 'testMode' => true], debugMode: true);

Assert::type(Skautis::class, $container->getService('skautis.skautis'));

$factory = $container->getService('skautis.webServiceFactory');
Assert::type(WebServiceFactory::class, $factory);
// the extension's dispatcher is already in the factory, so the library refuses another one
Assert::exception(
    fn () => $factory->setEventDispatcher(new EventDispatcher()),
    InvalidArgumentException::class,
    'Event dispatcher is already set.'
);

if (! class_exists(Bar::class)) {
    Assert::false($container->hasService('skautis.panel'));
    Assert::false($container->hasService('skautis.queryLog'));

    return;
}

Assert::true($container->hasService('skautis.panel'));
$panel = $container->getService('skautis.panel');
Assert::type(Panel::class, $panel);
Assert::same($panel, $container->getByType(Bar::class)->getPanel(Panel::class), 'the panel is in the Tracy bar after initialize()');

$container->getByType(EventDispatcher::class)
    ->dispatch(new RequestPostEvent('UnitDetail', [['ID' => 1]], null, 0.25, []));

$log = $container->getService('skautis.queryLog');
Assert::type(QueryLog::class, $log);
Assert::count(1, $log->getQueries());
Assert::contains('1 skautIS', $panel->getTab());
Assert::contains('UnitDetail', $panel->getPanel());
