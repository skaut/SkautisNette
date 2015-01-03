<?php

$rootDir = __DIR__ . '/..';
$testsDir = __DIR__;

if (getenv('NETTE') !== 'master') {
	$composerFile = 'composer-nette-' . getenv('NETTE') . '.json';

	unlink("$rootDir/composer.json");
	copy("$testsDir/$composerFile", "$rootDir/composer.json");

	echo "Using tests/$composerFile\n";

} else {
	echo "Using default composer.json\n";
}
