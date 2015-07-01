<?php

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$configurator = new Nette\Configurator;
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(FALSE);
$configurator->addConfig(__DIR__ . '/files/config.neon');
$container = $configurator->createContainer();

Assert::type('Skautis\Skautis', $container->getService('skautis.skautis'));
Assert::false($container->hasService('skautis.panel'));
