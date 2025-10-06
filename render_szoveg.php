<?php
require_once __DIR__ . '/inc/karaktercsere.php';
require_once 'mondat_elemzes.php';
require_once 'hangosszotar.php';
require_once 'enrich_with_icons_csv.php';
require_once 'suffix.php';

$ismert_szavak_lista_tomb = mp3_hangosszotar_reader();
$suffixek = betoltott_suffixek();

$szofaji_szotar = file_exists('json/szofaji_szotar.json')
    ? json_decode(file_get_contents('json/szofaji_szotar.json'), true)
    : [];

$szofaji_szotar_en = file_exists('json/szofaji_szotar_en.json')
    ? json_decode(file_get_contents('json/szofaji_szotar_en.json'), true)
    : [];

$szofaj_tooltip = file_exists('json/szofaj_tooltip.json')
    ? json_decode(file_get_contents('json/szofaj_tooltip.json'), true)
    : [];

$fajlnev = $_GET['fajl'] ?? 'elemzes.txt';
if (!preg_match('/^[a-zA-Z0-9_\-]+\.txt$/', $fajlnev)) {
    die("❌ Érvénytelen fájlnév.");
}

$eleresi_ut = __DIR__ . '/txt/' . $fajlnev;
if (!file_exists($eleresi_ut)) {
    die("❌ A fájl nem található: $fajlnev");
}

$szoveg = karaktercsere_folio(file_get_contents($eleresi_ut));

$kiemeltek = [];
if (file_exists('kiemeltek.json')) {
    $json = file_get_contents('kiemeltek.json');
    $kiemeltek = json_decode($json, true);
    if (!is_array($kiemeltek)) $kiemeltek = [];
}

$szotar = file_exists('szotar_final.json')
    ? json_decode(file_get_contents('szotar_final.json'), true)
    : [];

$sorok = explode("\n", $szoveg);

foreach ($sorok as $sor) {
    $sor = trim($sor);
    if ($sor === '') continue;

    $szavak = elemez_mondat($sor);
    echo "<p>";

    foreach ($szavak as $elem) {
        $szo = $elem['text'];
        $tisztitott = $elem['clean'];
        $role = $elem['role'];

        // 🔍 Fordítás
        $forditas_hu = $forditas_en = $forditas_hi = '';
        foreach ($szotar as $entry) {
            if (($entry['hu'] ?? '') === $tisztitott) {
                $forditas_en = $entry['en'] ?? '';
                $forditas_hi = $entry['hi'] ?? '';
                break;
            }
            if (($entry['en'] ?? '') === $tisztitott) {
                $forditas_hu = $entry['hu'] ?? '';
                $forditas_hi = $entry['hi'] ?? '';
                break;
            }
            if (($entry['hi'] ?? '') === $tisztitott) {
                $forditas_en = $entry['en'] ?? '';
                $forditas_hu = $entry['hu'] ?? '';
                break;
            }
        }

        // 🧠 Szófaj és tooltip nyelvfüggően
        $is_en = preg_match('/^[a-zA-Z\-]+$/', $tisztitott);
        if ($is_en) {
    $szofaj = $szofaji_szotar_en[$tisztitott] ?? '';
    if ($szofaj === '') {
        foreach ($suffixek as $veg => $info) {
            if ($info['lang'] === 'en' && substr($tisztitott, -strlen($veg)) === $veg) {
                $szofaj = $info['type'] ?? '';
                break;
            }
        }
    }
    $tooltip = $szofaj_tooltip['en'][$szofaj] ?? '';
} else {
    $szofaj = $szofaji_szotar[$tisztitott] ?? '';
    if ($szofaj === '') {
        foreach ($suffixek as $veg => $info) {
            if ($info['lang'] === 'hu' && substr($tisztitott, -strlen($veg)) === $veg) {
                $szofaj = $info['type'] ?? '';
                break;
            }
        }
    }
    $tooltip = $szofaj_tooltip['hu'][$szofaj] ?? '';
}


        // 🎨 Osztályok összeállítása
        $classok = ['word'];
        $tisztitott_variaciok = [
            $tisztitott,
            rtrim($tisztitott, ';'),
            rtrim($tisztitott, ','),
            rtrim($tisztitott, '.'),
            rtrim($tisztitott, ':'),
        ];
        foreach ($tisztitott_variaciok as $valtozat) {
            if (in_array($valtozat, $kiemeltek)) {
                $classok[] = 'selected';
                break;
            }
        }
        if ($szofaj !== '') $classok[] = $szofaj;
        if ($role !== '') $classok[] = $role;

        $class_attr = implode(' ', array_unique($classok));

        // 📤 Megjelenítés
        $szo = mp3_hangosszotar_replace(' ' . $szo);
        if (strlen($szo) > 3) {
            $szo = enrich_with_icons_html($szo, $iconMap);
        }
        $megjeleno = trim($szo);

        if (in_array($tisztitott, $kiemeltek)) {
            if ($forditas_hu !== '' && $szo !== $forditas_hu) {
                $megjeleno .= " <span class='forditas'> $forditas_hu</span> ";
                $megjeleno .= " <button class='speak' data-text='$forditas_hu' data-lang='en-US'>🔊</button> ";
            } elseif ($forditas_en !== '' && $szo !== $forditas_en) {
                $megjeleno .= " <span class='forditas'> $forditas_en</span>";
                $megjeleno .= " <button class='speak' data-text='$forditas_en' data-lang='en-US'>🔊</button> ";
            } elseif ($forditas_hi !== '' && $szo !== $forditas_hi) {
                $megjeleno .= " <span class='forditas'> $forditas_hi</span>";
                $megjeleno .= " <button class='speak' data-text='$forditas_hi' data-lang='hi-IN'>🔊</button> ";
            }
        }

        echo "<span class='$class_attr'" . ($tooltip ? " title='$tooltip'" : "") . ">$megjeleno</span> ";
    }

    echo "</p>\n";
}
?>
