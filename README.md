[![Latest Stable Version](https://poser.pugx.org/skaut/skautis-nette/v/stable.svg)](https://packagist.org/packages/skaut/skautis-nette)
[![Total Downloads](https://poser.pugx.org/skaut/skautis-nette/downloads.svg)](https://packagist.org/packages/skaut/skautis-nette)
[![Latest Unstable Version](https://poser.pugx.org/skaut/skautis-nette/v/unstable.svg)](https://packagist.org/packages/skaut/skautis-nette)
[![License](https://poser.pugx.org/skaut/skautis-nette/license.svg)](https://packagist.org/packages/skaut/skautis-nette)

SkautisNette
============

Rozšíření pro [Nette](https://nette.org) integrující [knihovnu skaut/skautis](https://github.com/skaut/Skautis) pro práci se skautISem:
DI extension, session adaptér, PSR-14 dispatcher pro Tracy panel a vlastní posluchače, PSR-16 adaptér pro `nette/caching`
a JSON fixtury nahrazující skautIS v testech.


# Požadavky

- PHP 8.4 a novější s rozšířením `soap`
- [`skaut/skautis`](https://github.com/skaut/Skautis) 3.1 a novější
- Nette 3.2 a novější (`nette/di ^3.2`, `nette/http ^3.3`, `nette/schema ^1.3`), detaily v [composer.json](./composer.json)


# Instalace

Knihovna `skaut/skautis` zatím není na Packagistu, do `composer.json` aplikace proto přidejte její repozitář:

```json
"repositories": [
    {"type": "vcs", "url": "https://github.com/skaut/Skautis.git", "no-api": true}
]
```

Pak nainstalujte rozšíření (do verze 3.0 se jmenovalo `skautis/nette`):

```bash
composer require skaut/skautis-nette:^3.1
```

Zaregistrujte a nastavte rozšíření v konfiguračním souboru. Minimální konfigurace:

```neon
extensions:
    skautis: Skaut\SkautisNette\SkautisExtension

skautis:
    applicationId: abcd-...-abcd   # AppId přidělené administrátorem skautISu
    testMode: true                 # true = test-is.skaut.cz, false = ostrý is.skaut.cz
```


# Návod na použití

Podrobný přehled voleb, služeb, posluchačů událostí, cache a fixtur je v [dokumentaci](docs/README.md).
Změny mezi verzemi popisuje [CHANGELOG](CHANGELOG.md).

Dokumentaci samotné knihovny najdete na [https://github.com/skaut/Skautis](https://github.com/skaut/Skautis).


# Vývoj

Testy a PHPStan běží v Dockeru, PHP na hostiteli není potřeba:

```bash
make build install   # jednou (PHP=8.5 pro druhou verzi z CI)
make ci              # PHPStan + Nette Tester
```

S PHP na hostiteli stačí `composer install` a `composer ci`.
