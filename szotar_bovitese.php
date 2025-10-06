<?php
// 📁 Fájlok elérési útjai
$szovegfajl = __DIR__ . '/txt/elemzes.txt';
$szotar_path = __DIR__ . '/json/szofaji_szotar.json';

// 📥 Szöveg betöltése
$szoveg = file_exists($szovegfajl) ? file_get_contents($szovegfajl) : '';

// 📥 Szótár betöltése
$szotar = file_exists($szotar_path)
    ? json_decode(file_get_contents($szotar_path), true)
    : [];

// 🔍 Szavak kigyűjtése
$szavak = preg_split('/\s+/', $szoveg);
$tisztitottak = [];
foreach ($szavak as $szo) {
    $tisztitott = mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo));
    if ($tisztitott !== '') $tisztitottak[] = $tisztitott;
}

// 🧠 Ismeretlen szavak kiszűrése
$uj_szavak = array_diff(array_unique($tisztitottak), array_keys($szotar));

// 🧩 Toldalék-alapú szófaj javaslat
function javasolt_szofaj($szo) {
    if (preg_match('/(ta|te|ják|ik|ott|ett|ták)$/u', $szo)) return 'ige';
    if (preg_match('/(t|ot|et|at|át|ét)$/u', $szo)) return 'főnév+tárgyeset';
    return 'főnév';
}

// 💾 Mentés POST-tal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['szofaj'] ?? [] as $szo => $szofaj) {
        $szotar[$szo] = trim($szofaj);
    }
    file_put_contents($szotar_path, json_encode($szotar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "<p style='color:green;'>✅ Szótár frissítve!</p>";
}

// 📤 Megjelenítés
echo "<h2>🧠 Szófaji szótár bővítése</h2>";
echo "<form method='post'><table border='1' cellpadding='6'>";
echo "<tr><th>Szó</th><th>Javasolt szófaj</th><th>Szerkeszthető</th></tr>";
foreach ($uj_szavak as $szo) {
    $javaslat = javasolt_szofaj($szo);
    echo "<tr>
        <td>$szo</td>
        <td>$javaslat</td>
        <td><input type='text' name='szofaj[$szo]' value='$javaslat' /></td>
    </tr>";
}
echo "</table><br><button type='submit'>💾 Mentés a szótárba</button></form>";
?>
