<?php
// 📁 Fájlok elérési útjai
$kulso_lista = __DIR__ . '/import/magyar-szavak.txt';
$szotar_path = __DIR__ . '/json/szofaji_szotar.json';
$elemzes_fajl = __DIR__ . '/txt/elemzes.txt';

// 📥 Szótár betöltése
$szotar = file_exists($szotar_path)
    ? json_decode(file_get_contents($szotar_path), true)
    : [];

// 📥 Elemzendő szöveg betöltése
$elemzes_szoveg = file_exists($elemzes_fajl)
    ? file_get_contents($elemzes_fajl)
    : '';

// 🔍 Szavak kigyűjtése az elemzendő szövegből
$elemzes_szavak = preg_split('/\s+/', $elemzes_szoveg);
$elemzes_tisztitott = array_map(fn($szo) => mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo)), $elemzes_szavak);
$elemzes_tisztitott = array_filter($elemzes_tisztitott);

// 📥 Külső szólista betöltése
$kulso_szavak = file($kulso_lista, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$kulso_tisztitott = array_map(fn($szo) => mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo)), $kulso_szavak);
$kulso_tisztitott = array_filter($kulso_tisztitott);

// 🔍 Metszet: csak azok a külső szavak, amik az elemzett szövegben is szerepelnek
$relevans_szavak = array_intersect($kulso_tisztitott, $elemzes_tisztitott);

// 🔍 Ismeretlen szavak kiszűrése
$uj_szavak = array_diff(array_unique($relevans_szavak), array_keys($szotar));

// 🧠 Toldalék-alapú szófaj javaslat
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

// 📊 Előnézet
echo "<h2>🔍 Előnézet</h2>";
echo "<p>Elemzett szöveg szavainak száma: <strong>" . count($elemzes_tisztitott) . "</strong></p>";
echo "<p>Releváns külső szavak száma: <strong>" . count($relevans_szavak) . "</strong></p>";
echo "<p>Új, ismeretlen szavak száma: <strong>" . count($uj_szavak) . "</strong></p>";

if (count($uj_szavak) > 0) {
    echo "<p>Első 10 új szó javasolt szófajjal:</p><ul>";
    foreach (array_slice($uj_szavak, 0, 10) as $szo) {
        $javaslat = javasolt_szofaj($szo);
        echo "<li><strong>$szo</strong> → <em>$javaslat</em></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nincs új szó a szótárhoz képest.</p>";
}

// 📤 Interaktív szerkesztés
echo "<h2>🌐 Szótárbővítés</h2>";
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
