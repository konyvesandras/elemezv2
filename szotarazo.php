<?php
// 🚀 Szótár generáló script – csak a kiemelt szavakra

// 🔧 Segédfüggvények és konfiguráció betöltése
require_once __DIR__ . '/szotar_utils.php';   // pl. detectLanguage(), googleTranslateBatchSmart()
require_once __DIR__ . '/szotar_config.php';  // pl. $inputFile, $finalFile, $targetLangs

// 📦 Kiemelt szavak betöltése
$kiemeltek = file_exists($inputFile)
    ? json_decode(file_get_contents($inputFile), true)
    : [];
if (!is_array($kiemeltek)) $kiemeltek = [];

// 📦 Szótár cache betöltése
$cache = file_exists($finalFile)
    ? json_decode(file_get_contents($finalFile), true)
    : [];
if (!is_array($cache)) $cache = [];

// 📊 Állapot inicializálása
$progress = 0;
$total = count($kiemeltek);
$history = [];

// 🔁 Szavak feldolgozása
foreach ($kiemeltek as $szo) {
    $szo = trim($szo);
    if ($szo === '') continue;

    // ⚠️ Ha már le van fordítva, kihagyjuk
    $marVan = false;
    foreach ($cache as $entry) {
        if (in_array($szo, $entry, true)) {
            $marVan = true;
            break;
        }
    }
    if ($marVan) {
        $progress++;
        continue;
    }

    // 🧠 Nyelv detektálása
    $srcLang = detectLanguage($szo);

    // 🌐 Fordítás minden célnyelvre
    $forditasok = [];
    foreach ($targetLangs as $tl) {
        $res = googleTranslateBatchSmart([$szo], $srcLang, $tl);
        $forditas = $res[0] ?? '';

        // 🔁 Ha nincs értelmes fordítás, fallback transliteráció
        if ($forditas === '' || $forditas === $szo) {
            $forditas = transliterateDevanagari($szo);
        }

        $forditasok[$tl] = $forditas;
    }

    // 📦 Új bejegyzés összeállítása
    $entry = [];
    foreach ($targetLangs as $tl) {
        $entry[$tl] = $forditasok[$tl] ?? '';
    }
    $entry[$srcLang] = $szo;

    $cache[] = $entry;

    // 📊 Állapot frissítése
    $progress++;
    $history[] = $szo;
    updateStatusFile($statusFile, $srcLang, implode(',', $targetLangs), $szo, $forditasok, "$progress / $total", $history);
}

// 💾 Szótár mentése
file_put_contents($finalFile, json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// 📤 Válasz JSON-ben
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => "Szótár frissítve a kiemelt szavak alapján.",
    'progress' => "$progress / $total",
    'updated' => $history
]);
