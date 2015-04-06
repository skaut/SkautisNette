<?php

use Tester\Assert;
use Nette\Http;
use Skautis\Nette\CacheAdapter;


require __DIR__ . '/../bootstrap.php';

$storage = new \Nette\Caching\Storages\FileStorage(TEMP_DIR);
$cache = new CacheAdapter($storage, 'skautis');

Assert::equal('skautis', $cache->getNamespace());
Assert::equal($storage, $cache->getStorage());

Assert::equal(NULL, $cache->get('unknown'));

$cache->set('key', 'value');
Assert::equal('value', $cache->get('key'));

$cache->clean();
Assert::equal(NULL, $cache->get('key'));

$cache->setExpiration('1 second');
$cache->set('key', 'value');
sleep(2);
Assert::equal(NULL, $cache->get('key'));
