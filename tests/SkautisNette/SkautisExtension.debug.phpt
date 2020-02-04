<?php

declare(strict_types=1);

use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$configurator = new Nette\Configurator;
$configurator->setTempDirectory(TEMP_DIR);
$configurator->setDebugMode(TRUE);
$configurator->addConfig(__DIR__ . '/files/config.neon');
$container = $configurator->createContainer();

if (class_exists('Tracy\Debugger')) {
  Assert::true($container->hasService('skautis.panel'));
  Assert::false($container->isCreated('skautis.panel'));
}
else {
  Assert::false($container->hasService('skautis.panel'));
}

Assert::true($container->hasService('skautis.skautis'));
Assert::type('Skautis\Skautis', $container->getService('skautis.skautis'));

if (class_exists('Tracy\Debugger')) {
  Assert::true($container->isCreated('skautis.panel'));
  Assert::type('Skautis\Nette\Tracy\Panel', $container->getService('skautis.panel'));
}
