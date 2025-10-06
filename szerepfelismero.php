<?php
// 🔧 Beállítások: fájlok elérési útjai
$szovegfajl = __DIR__ . '/txt/elemzes.txt'; // bemeneti szöveg
$json_path = __DIR__ . '/json/mondatszerepek.json'; // mondatszerepek tárolása

// 📥 Szöveg betöltése fájlból
$szoveg = file_exists($szovegfajl) ? file_get_contents($szovegfajl) : '';

// 📥 Mondatszerepek betöltése JSON-ból (ha van)
$szerepek = file_exists($json_path)
    ? json_decode(file_get_contents($json_path), true)
    : ['alanyok'=>[], 'allitmanyok'=>[], 'targyak'=>[]];

// 🧠 Toldalék-alapú szerepfelismerés (egyszerű szabályok)
function szerep_toldalek_alapjan($szo) {
    $szo = mb_strtolower($szo);
    if (preg_match('/(ta|te|ják|ik|ott|ett|ták)$/u', $szo)) return 'allitmany';
    if (preg_match('/(t|ot|et|at|át|ét)$/u', $szo)) return 'targy';
    return 'alany'; // alapértelmezett
}

// 🧩 Kulcs normalizálása a JSON struktúrához
function normalizalt_kulcs(string $szerep): string {
    $kulcsok = [
        'alany' => 'alanyok',
        'allitmany' => 'allitmanyok',
        'targy' => 'targyak'
    ];
    return $kulcsok[$szerep] ?? $szerep . 'ok';
}

// 📤 Újonnan felismert szavak gyűjtése
$uj_szavak = ['alanyok'=>[], 'allitmanyok'=>[], 'targyak'=>[]];

// 🔍 Szavak feldolgozása a szövegből
$szavak = preg_split('/\s+/', $szoveg);
foreach ($szavak as $szo) {
    // 🔒 Szó tisztítása: csak betűk, számok, kötőjel
    $tisztitott = mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo));
    if ($tisztitott === '') continue;

    // 🧠 Szerep meghatározása toldalék alapján
    $szerep = szerep_toldalek_alapjan($tisztitott);
    $kulcs = normalizalt_kulcs($szerep);

    // 📌 Ha még nincs benne, hozzáadjuk
    if (!isset($szerepek[$kulcs])) $szerepek[$kulcs] = [];
    if (!in_array($tisztitott, $szerepek[$kulcs])) {
        $szerepek[$kulcs][] = $tisztitott;
        $uj_szavak[$kulcs][] = $tisztitott;
    }
}

// 💾 Frissített mondatszerepek mentése JSON-ba
file_put_contents($json_path, json_encode($szerepek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 📊 Visszajelzés: mit tanultunk
echo "<h2>✅ Újonnan felismert mondatszerepek:</h2><ul>";
foreach ($uj_szavak as $szerep => $szavak) {
    if (count($szavak) > 0) {
        echo "<li><strong>$szerep</strong>: " . implode(', ', $szavak) . "</li>";
    }
}
echo "</ul>";
?>
