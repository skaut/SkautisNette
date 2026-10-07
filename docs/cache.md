# Cachování požadavků pomocí `nette/caching`

Knihovna nabízí dekorátor `Skaut\Skautis\Wsdl\Decorator\Cache\CacheDecorator`, který ukládá odpovědi do libovolné
[PSR-16](https://www.php-fig.org/psr/psr-16/) cache. `Skaut\SkautisNette\Cache\CacheAdapter` je PSR-16 adaptér nad
`Nette\Caching\Cache`, takže je možné použít kterékoli úložiště z balíčku `nette/caching` (3.3 a novější).

## Příklad

```php
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Skaut\Skautis\Wsdl\Decorator\Cache\CacheDecorator;
use Skaut\SkautisNette\Cache\CacheAdapter;

// webová služba ze skautisu
$webService = $skautis->User;

// cache nad zvoleným úložištěm
$netteCache = new Cache(new FileStorage(__DIR__.'/../temp/cache'), 'skautis');
$cache = new CacheAdapter($netteCache);

// cachovaná webová služba s platností odpovědí 1 den
$ttl = 60 * 60 * 24;
$cachedWebService = new CacheDecorator($webService, $cache, $ttl);

// používá se stejně jako necachovaná služba
$cachedWebService->call('UserDetail', [['ID' => 1940]]);
```

## Chování adaptéru

- Klíče musí být neprázdné řetězce bez znaků `{}()/\@:` (PSR-16); jinak `Skaut\SkautisNette\Cache\InvalidKeyException`.
- TTL je `int` v sekundách, `DateInterval` nebo `null`. Nulové a záporné TTL položku smaže. Druhý argument
  konstruktoru (`?int $defaultTtl`) je TTL pro volání `set()` bez TTL; `null` nechá platnost na úložišti.
- Nette neumí uložit `null`, proto `has()` vrací `false` a `get()` výchozí hodnotu i pro uložené `null`.
- Selhání úložiště při zápisu vrátí `false` z `set()`, `delete()` a `clear()`; selhání při čtení vyhodí
  `Skaut\SkautisNette\Cache\CacheException`.
