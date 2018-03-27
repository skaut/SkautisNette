<?php

use Tester\Assert;
use Skautis\Skautis;
use Skautis\Nette\Tracy\Panel;


require __DIR__ . '/../bootstrap.php';


$configurator = new Nette\Configurator;
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(TRUE);
$configurator->addConfig(__DIR__ . '/files/config.neon');
$container = $configurator->createContainer();

Assert::false($container->isCreated('skautis.panel'));
Assert::type(Skautis::class, $container->getService('skautis.skautis'));
Assert::true($container->isCreated('skautis.panel'));
Assert::type(Panel::class, $container->getService('skautis.panel'));
