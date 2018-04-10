[![Latest Stable Version](https://poser.pugx.org/skautis/nette/v/stable.svg)](https://packagist.org/packages/skautis/nette) [![Total Downloads](https://poser.pugx.org/skautis/nette/downloads.svg)](https://packagist.org/packages/skautis/nette) [![Latest Unstable Version](https://poser.pugx.org/skautis/nette/v/unstable.svg)](https://packagist.org/packages/skautis/nette) [![License](https://poser.pugx.org/skautis/nette/license.svg)](https://packagist.org/packages/skautis/nette)

SkautisNette
============

Rozšíření pro [Nette](https://github.com/nette/nette) integrující [PHP knihovnu pro připojení ke SkautISu](https://github.com/skaut/Skautis).


# Požadavky

[Nette Framework](https://github.com/nette/nette) verze 3.0 nebo vyšší. Detaily v [composer.json](./composer.json).


# Instalace

Nainstalujte balíček přes composer ``composer require skautis/nette:^3.0``, zaregistrujte a nastavte rozšíření (extension) v konfiguračním souboru.

Ukázka minimální konfigurace:
```
extensions:
    skautis: Skautis\Nette\SkautisExtension

skautis:
    applicationId : abcd-...-abcd # AppId přidělené administrátorem skautISu
```


# Návod na použití

Podrobný přehled použití tohoto rozšíření v [dokumentaci](docs/README.md).

Dokumentaci samotné knihovny najdete na [https://github.com/skaut/Skautis](https://github.com/skaut/Skautis).
