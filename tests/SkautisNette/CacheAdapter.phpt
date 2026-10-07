<?php

declare(strict_types=1);

use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Skaut\SkautisNette\Cache\CacheAdapter;
use Skaut\SkautisNette\Cache\InvalidKeyException;
use Skaut\SkautisNette\Cache\InvalidTTLException;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

$netteCache = new Cache(new MemoryStorage(), 'skautis');
$cache = new CacheAdapter($netteCache);

test('missing item', function () use ($cache): void {
    Assert::null($cache->get('unknown'));
    Assert::same(15, $cache->get('unknown', 15));
    Assert::false($cache->has('unknown'));
});

test('set, get, delete', function () use ($cache): void {
    Assert::true($cache->set('key', 'value'));
    Assert::same('value', $cache->get('key'));
    Assert::true($cache->has('key'));

    Assert::true($cache->delete('key'));
    Assert::null($cache->get('key'));
    Assert::false($cache->has('key'));
});

test('positive TTL keeps the item', function () use ($cache): void {
    Assert::true($cache->set('seconds', 'value', 60));
    Assert::same('value', $cache->get('seconds'));

    Assert::true($cache->set('interval', 'value', new DateInterval('PT1M')));
    Assert::same('value', $cache->get('interval'));
});

test('zero or negative TTL removes the item (PSR-16)', function () use ($cache): void {
    Assert::true($cache->set('zero', 'value', 0));
    Assert::false($cache->has('zero'));

    $cache->set('negative', 'value');
    Assert::true($cache->set('negative', 'value', -5));
    Assert::false($cache->has('negative'));

    $past = new DateInterval('PT1M');
    $past->invert = 1;
    $cache->set('past', 'value');
    Assert::true($cache->set('past', 'value', $past));
    Assert::false($cache->has('past'));
});

test('multiple items', function () use ($cache): void {
    Assert::true($cache->setMultiple(['a' => 1, 'b' => 2]));
    Assert::same(['a' => 1, 'b' => 2, 'c' => 'default'], $cache->getMultiple(['a', 'b', 'c'], 'default'));
    Assert::false($cache->has('c'), 'getMultiple() must not store the default value');

    Assert::true($cache->deleteMultiple(['a', 'b']));
    Assert::same(['a' => null, 'b' => null], $cache->getMultiple(['a', 'b']));
});

test('clear', function () use ($cache): void {
    $cache->set('x', 1);
    Assert::true($cache->clear());
    Assert::null($cache->get('x'));
});

test('invalid keys and TTL', function () use ($cache, $netteCache): void {
    Assert::exception(fn () => $cache->get(''), InvalidKeyException::class);
    Assert::exception(fn () => $cache->set('a{b}', 1), InvalidKeyException::class);
    Assert::exception(fn () => $cache->has('a:b'), InvalidKeyException::class);
    Assert::exception(fn () => $cache->delete('a/b'), InvalidKeyException::class);
    Assert::exception(fn () => $cache->getMultiple([1]), InvalidKeyException::class);
    Assert::exception(fn () => new CacheAdapter($netteCache, 0), InvalidTTLException::class);
});
