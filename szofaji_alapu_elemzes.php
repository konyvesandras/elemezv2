<?php
// 📁 Fájlok elérési útjai
$szovegfajl = __DIR__ . '/txt/elemzes.txt';
$szotar_path = __DIR__ . '/json/szofaji_szotar.json';

// 📥 Szöveg betöltése
$szoveg = file_exists($szovegfajl) ? file_get_contents($szovegfajl) : '';

// 📥 Szófaji szótár betöltése
$szotar = file_exists($szotar_path)
    ? json_decode(file_get_contents($szotar_path), true)
    : [];

// 🧠 Szófaj → mondatszerep leképezés
function szerep_szofaj_alapjan(string $szofaj): string {
    $szofaj = mb_strtolower($szofaj);
    if (str_contains($szofaj, 'ige')) return 'allitmany';
    if (str_contains($szofaj, 'tárgyeset')) return 'targy';
    if (str_contains($szofaj, 'főnév')) return 'alany';
    return 'egyéb';
}

// 📤 Eredmény gyűjtése
$eredmeny = [];

// 🔍 Szavak feldolgozása
$szavak = preg_split('/\s+/', $szoveg);
foreach ($szavak as $szo) {
    // 🔒 Tisztítás: csak betűk, számok, kötőjel
    $tisztitott = mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo));
    if ($tisztitott === '') continue;

    // 🔍 Szófaj keresése
    $szofaj = $szotar[$tisztitott] ?? 'ismeretlen';
    $szerep = $szofaj !== 'ismeretlen' ? szerep_szofaj_alapjan($szofaj) : 'ismeretlen';

    // 📌 Eredmény mentése
    $eredmeny[] = [
        'szo' => $szo,
        'tisztitott' => $tisztitott,
        'szofaj' => $szofaj,
        'szerep' => $szerep
    ];
}

// 📊 Megjelenítés
echo "<h2>🔍 Szófaji alapú mondatszerep-elemzés</h2><table border='1' cellpadding='6'>";
echo "<tr><th>Szó</th><th>Tisztított</th><th>Szófaj</th><th>Szerep</th></tr>";
foreach ($eredmeny as $elem) {
    echo "<tr>
        <td>{$elem['szo']}</td>
        <td>{$elem['tisztitott']}</td>
        <td>{$elem['szofaj']}</td>
        <td><strong>{$elem['szerep']}</strong></td>
    </tr>";
}
echo "</table>";
?>
