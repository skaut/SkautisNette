SkautisNette
=======

Rozšíření do [Nette](https://github.com/nette/nette) o [PHP knihovnu pro připojení ke Skautisu](https://github.com/skaut/skautis).

# Jak rozšíření připojit?
Pro připojení do Nette, stačí zaregistrovat rozšíření(extension) v konfiguračním souboru a nastavit ho.

```
extensions:
    skautis: SkautIS\Nette\SkautisExtension22 # yourNameOfExtension : SkautIS\Nette\SkautisExtension22

skautis:
    applicationId : abcd-...-abcd #ID_Aplication assigned by skautis administrator
    testMode : true #using http://test-is.skaut.cz/
    profiler: true
```
Ukázkový konfigurační soubor najdete v adresáři repozitáže [src/SkautIS/Nette/config.sample.neon](https://github.com/sinacek/SkautIS/blob/master/src/SkautIS/Nette/config.sample.neon).

Po registraci rozšíření se v testovacím modu Nette automaticky aktivuje skautis panel, který sleduje všechny dotazy na skautis.

![Skautis panel pro ladění aplikace](skautis-panel.png)

Všechny informace o práci s knihovnou najdete na [https://github.com/skaut/skautis](https://github.com/skaut/skautis)