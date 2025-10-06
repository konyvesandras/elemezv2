<?php
// 🌍 Beállítások és fájlnevek

// Bemenet: mindig a kiemelt szavak listája
$inputFile   = __DIR__ . "/kiemeltek.json";

// Köztes fájl (ha szükséges lépcsőzetes feldolgozáshoz)
$outputFile  = __DIR__ . "/szotar.json";

// Végső szótár, amit a frontend használ
$finalFile   = __DIR__ . "/szotar_final.json";

// Állapotfájl a feldolgozás közbeni progresszhez
$statusFile  = __DIR__ . "/szotar_status.json";

// Célnyelvek – ezekre készül fordítás
// hu = magyar, en = angol, hi = hindi, ne = nepáli
// (ha nem kell nepáli, egyszerűen vedd ki a listából)
$targetLangs = ['hu', 'en', 'hi'];

// Batch méret: egyszerre ennyi szót küldünk a Google Translate-nek
// (25 jó kompromisszum: gyors, de nem túl nagy kérés)
$batchSize   = 25;

// ⏱️ Ne legyen időkorlát a futásra
set_time_limit(0);
