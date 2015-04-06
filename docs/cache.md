# Cachování požadavků pomocí `nette/caching`

Pro cachování požadavků na SkautIS je možné použít libovolné uložiště z balíčku `nette/caching`.

## Příklad

```php
// Získáme webovou službu ze skautisu
$webService = $skautis->User;

// Vytvoříme cache používající zvoleného uložiště
$cache = new Skautis\Nette\CacheAdapter($storage, 'namespace');

// Nastavíme platnost cachovaných dat
$cache->setExpiration('1 day');

// Vytvoříme cachovanou webovou službu
$cachedWebService = new CacheDecorator($webService, $cache);

// Nyní můžeme použít cachovanou webovou službu jako klasickou webovou službu
$cachedWebService->call('UserDetail', ['ID' => 1940]);
```
