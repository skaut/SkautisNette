<?php

declare(strict_types=1);

use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Skautis\Nette\Cache\CacheAdapter;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';

$storage = new MemoryStorage();
$netteCache = new Cache($storage, 'skautis');
$cache = new CacheAdapter($netteCache);

Assert::equal(null, $cache->get('unknown'));
Assert::equal(15, $cache->get('unknown', 15));

$cache->set('key', 'value');
Assert::equal('value', $cache->get('key'));

$netteCache->clean([Nette\Caching\Cache::ALL => true]);
Assert::equal(null, $cache->get('key'));

$cache->set('key2', 'value', 1);
sleep(2);
Assert::equal(null, $cache->get('key'));

$cache->setMultiple(['a' => 1, 'b' => 2]);
$values = $cache->getMultiple(['a', 'b']);
Assert::equal(1, $values['a']);
Assert::equal(2, $values['b']);
Assert::equal(true, $cache->deleteMultiple(['a', 'b']));
$values = $cache->getMultiple(['a', 'b']);
Assert::equal(null, $values['a']);
Assert::equal(null, $values['b']);


$cache->set('key3', 10);
Assert::equal(10, $cache->get('key3'));
Assert::equal(true, $cache->delete('key3'));
Assert::equal(null, $cache->get('key3'));
