# Changelog

## v3.1.0

Rozšíření je navázané na knihovnu [`skaut/skautis` 3.1](https://github.com/skaut/Skautis/releases/tag/v3.1.0).

* Zpětně nekompatibilní: balíček se jmenuje `skaut/skautis-nette` a namespace je `Skaut\SkautisNette`, stejný vzor
  jako `skaut/skautis` a `Skaut\Skautis` (dřív `skautis/nette` a `Skautis\Nette`). V konfiguraci změňte
  `Skautis\Nette\SkautisExtension` na `Skaut\SkautisNette\SkautisExtension`.
* Požadováno PHP 8.4 a novější, `skaut/skautis` ^3.1, `nette/di` ^3.2, `nette/http` ^3.3, `nette/schema` ^1.3,
  `psr/simple-cache` ^3.0. Knihovna není na Packagistu, aplikace potřebuje blok `repositories` (viz README).
* Zpětně nekompatibilní: volba `testMode` je povinná. Dřívější výchozí `false` (ostrý skautIS) se lišilo od výchozí
  hodnoty knihovny (`true`); konfigurace bez `testMode` teď skončí chybou při kompilaci kontejneru místo tichého
  volání jiné instance skautISu.
* Zpětně nekompatibilní: `SessionAdapter` má signatury `set(string, mixed): void` a `get(string): mixed` podle
  `AdapterInterface` 3.1 a používá `SessionSection::set()`/`get()`. Sekce session se jmenuje podle třídy, tedy nově
  `Skaut\SkautisNette\SessionAdapter`; po nasazení se uživatelé jednou odhlásí.
* Nový `Skaut\SkautisNette\EventDispatcher` (služba `skautis.eventDispatcher`): jediný PSR-14 dispatcher, který knihovna
  přijímá, s `addListener(callable)` pro vlastní posluchače. Předává se továrně `WebServiceFactory` explicitně,
  takže autowiring cizího dispatcheru už nezpůsobí „Event dispatcher is already set.“
* Tracy panel přepsán: `Skaut\SkautisNette\Tracy\QueryLog` je posluchač dispatcheru a `Panel` se přidává do `Tracy\Bar`
  v `initialize()` kontejneru. Panel zvýrazní neúspěšné dotazy. Odstraněny `Skautis\Nette\Tracy\EventDispatcher`,
  `Skautis\Nette\Tracy\SkautisQuery` (nahrazeno `Skaut\SkautisNette\Tracy\Query`) a `Panel::register()`.
* Nové: volba `fixtures` a `Skaut\SkautisNette\Fixture\FixtureWebServiceFactory` vracejí odpovědi skautISu z JSON
  souborů místo SOAP volání, ukázková data jsou v `resources/fixtures`. Viz [docs/fixtures.md](docs/fixtures.md).
* `Cache\CacheAdapter` má signatury PSR-16 3.0, nulové a záporné TTL položku smaže, `getMultiple()` už neukládá
  výchozí hodnotu do cache a konstruktor přijímá `?int $defaultTtl`. `Cache\CacheException` implementuje
  `Psr\SimpleCache\CacheException` (dřív omylem `InvalidArgumentException`); `InvalidKeyException` a
  `InvalidTTLException` dědí od `Cache\InvalidArgumentException`.
* Vývoj: Nette Tester 2.5, PHPStan 2 (level max, bez baseline), GitHub Actions na PHP 8.4 a 8.5 s Tracy i bez ní,
  `Makefile` a `docker/Dockerfile` pro běh bez PHP na hostiteli. Odstraněny `tests/composer-nette-*.json`
  a `tests/prepare-composer.php`.

Starší verze changelog nemají, viz historie gitu.
