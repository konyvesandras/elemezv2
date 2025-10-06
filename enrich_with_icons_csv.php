<?php
// 1. Betöltjük az iconmap.json fájlt
$iconMap = json_decode(file_get_contents(__DIR__ . '/fixjson/iconmap.json'), true);


//echo enrich_with_icons_html('tanítás',$iconMap);



// 5. Feldolgozó függvény
function enrich_with_icons_html($text, $iconMap) {
    $words = preg_split('/\s+/', $text);
    $enriched = [];

    foreach ($words as $word) {
        $clean = mb_strtolower(trim($word, ".,!?()[]{}\"'"));
        $icons = $iconMap[$clean] ?? [];

        if ($icons) {
            $lastIcon = end($icons); // utolsó ikon kiválasztása
            $wrapped = '<span class="emoji">' . $lastIcon . '</span>';
            $enriched[] = $word . ' ' . $wrapped;
        } else {
            $enriched[] = $word;
        }
    }

    return implode(' ', $enriched);
}

?>
