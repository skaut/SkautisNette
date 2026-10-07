<?php

declare(strict_types=1);

// Run with: vendor/bin/tester tests -s -C

if (@! include __DIR__.'/../vendor/autoload.php') {
    echo 'Install dependencies using `composer install`';
    exit(1);
}

Tester\Environment::setup();
Tester\Environment::setupFunctions();
date_default_timezone_set('Europe/Prague');

define('TEMP_DIR', __DIR__.'/tmp/'.getmypid());
@mkdir(dirname(TEMP_DIR)); // @ - directory may already exist
Tester\Helpers::purge(TEMP_DIR);

/**
 * Builds a Nette container with the extension registered under the name `skautis`.
 *
 * @param array<string, mixed> $skautis the whole `skautis:` configuration section
 */
function createContainer(array $skautis, bool $debugMode): Nette\DI\Container
{
    $configurator = new Nette\Bootstrap\Configurator();
    $configurator->setTempDirectory(TEMP_DIR);
    $configurator->setDebugMode($debugMode);
    $configurator->addConfig(__DIR__.'/SkautisNette/files/config.neon');
    $configurator->addConfig(['skautis' => $skautis]);

    return $configurator->createContainer();
}
