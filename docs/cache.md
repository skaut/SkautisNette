# Cachování požadavků pomocí `nette/caching`

Pro cachování požadavků na SkautIS je možné použít libovolné uložiště z balíčku `nette/caching`.

## Příklad

```php
// Získáme webovou službu ze skautisu
$webService = $skautis->User;

$storage = new MemoryStorage();
$netteCache = new Cache($storage, 'namespace-skautis');

// Vytvoříme cache používající zvoleného uložiště
// S platností cachovaných dat 1 den
$ttl = 60*60*24; 
$cache = new CacheAdapter($netteCache, $ttl);

// Vytvoříme cachovanou webovou službu
$cachedWebService = new CacheDecorator($webService, $cache);

// Nyní můžeme použít cachovanou webovou službu jako klasickou webovou službu
$cachedWebService->call('UserDetail', ['ID' => 1940]);
```
