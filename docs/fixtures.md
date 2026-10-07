# Fixtury místo skautISu

Volba `fixtures` nahradí SOAP volání čtením JSON souborů. Hodí se pro testy a pro vývoj bez připojení
k test-is.skaut.cz.

```neon
skautis:
    applicationId: test
    testMode: true
    fixtures: %appDir%/../tests/fixtures/skautis
```

`{fixtures}/{Služba}/{Metoda}.json` obsahuje normalizovaný výsledek přesně tak, jak ho vrací knihovna: objekt,
seznam objektů nebo `null`. Varianta pro jeden argument, např. `OrganizationUnit/PersonAll__ID_Unit=1002.json`,
má přednost před obecným souborem; argumenty se zkoušejí v pořadí, v jakém byly předány. Chybějící soubor vyhodí
`Skaut\SkautisNette\Fixture\FixtureNotFoundException` s cestou, kterou je potřeba vytvořit.

`resources/fixtures` v balíčku obsahuje malou jednotku (středisko 1002 se dvěma oddíly) s osobami, kontakty,
rodiči, rolemi a přihlášením, které nikdy nevyprší. Přihlášení probíhá stejně jako s ostrým skautISem: POST
`skautIS_Token`, `skautIS_IDRole`, `skautIS_IDUnit` a `skautIS_DateLogout` na přihlašovací akci aplikace.

S fixturami se nevyvolávají události (`RequestPreEvent`, `RequestPostEvent`, `RequestFailEvent`), Tracy panel
tedy zůstane prázdný.
