<?php

declare(strict_types=1);

use Nette\DI\InvalidConfigurationException;
use Skaut\Skautis\Config;
use Skaut\Skautis\Skautis;
use Skaut\Skautis\User;
use Tester\Assert;
use Tracy\Bar;

require __DIR__.'/../bootstrap.php';

$container = createContainer(['applicationId' => 'test', 'testMode' => false], debugMode: false);

Assert::type(Skautis::class, $container->getService('skautis.skautis'));
Assert::type(User::class, $container->getService('skautis.user'));
Assert::false($container->hasService('skautis.panel'));
Assert::false($container->hasService('skautis.queryLog'));

$config = $container->getByType(Config::class);
Assert::same('test', $config->getAppId());
Assert::false($config->isTestMode());
Assert::true($config->isCacheEnabled());
Assert::true($config->isCompressionEnabled());

// the profiler can be switched on outside debug mode
if (class_exists(Bar::class)) {
    $container = createContainer(['applicationId' => 'test', 'testMode' => false, 'profiler' => true], debugMode: false);
    Assert::true($container->hasService('skautis.panel'));
}

// testMode has no default: a configuration without it must not compile
// (Nette Schema joins the path with non-breaking spaces around the separator)
Assert::exception(
    fn () => createContainer(['applicationId' => 'test'], debugMode: false),
    InvalidConfigurationException::class,
    "The mandatory item 'skautis\u{a0}›\u{a0}testMode' is missing."
);
