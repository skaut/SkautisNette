<?php

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$configurator = new Nette\Configurator;
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(TRUE);
$configurator->addConfig(__DIR__ . '/files/config.neon');
$container = $configurator->createContainer();

Assert::false($container->isCreated('skautis.panel'));
Assert::type('Skautis\Skautis', $container->getService('skautis.skautis'));
Assert::true($container->isCreated('skautis.panel'));
Assert::type('Skautis\Nette\Tracy\Panel', $container->getService('skautis.panel'));
