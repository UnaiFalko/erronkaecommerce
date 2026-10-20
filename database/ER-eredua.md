# E-R eredua (3.3.1)

Online dendaren beharretatik abiatuta: bezeroek erregistratu, produktuak kategoriaka ikusi, saskia osatu eta eskaerak egiten dituzte; administratzaileak produktuak kudeatzen ditu.

```
 KATEGORIAK 1 ────< N PRODUKTUAK 1 ────< N ESKAERA_LERROAK N >──── 1 ESKAERAK N >──── 1 ERABILTZAILEAK
```

| Entitatea | Gako nagusia | Atributu garrantzitsuak | Erlazioak (atzerriko gakoak) |
|---|---|---|---|
| erabiltzaileak | id | izena, email (UNIQUE), pasahitza_hash, rola | — |
| kategoriak | id | izena (UNIQUE) | — |
| produktuak | id | izena, deskribapena, prezioa, stock, irudia | kategoria_id → kategoriak.id |
| eskaerak | id | data, guztira, egoera | erabiltzailea_id → erabiltzaileak.id |
| eskaera_lerroak | id | kopurua, prezioa_unitarioa | eskaera_id → eskaerak.id, produktua_id → produktuak.id |

**Kardinalitateak**
- Kategoria bat → produktu asko (1:N).
- Erabiltzaile bat → eskaera asko (1:N).
- Eskaera ↔ Produktua: N:M, `eskaera_lerroak` taula bitartekariaren bidez.

`prezioa_unitarioa` lerroan gordetzen da, produktuaren prezioa geroago aldatuta ere eskaera historikoek jatorrizko prezioa mantentzeko.
