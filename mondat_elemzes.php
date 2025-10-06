<?php
function elemez_mondat($sor) {
    // 🔄 Külső JSON betöltése
    $json_path = __DIR__ . '/json/mondatszerepek.json';
    $szerepek = file_exists($json_path)
        ? json_decode(file_get_contents($json_path), true)
        : [];

    $alanyok = $szerepek['alanyok'] ?? [];
    $allitmanyok = $szerepek['allitmanyok'] ?? [];
    $targyak = $szerepek['targyak'] ?? [];

    $szavak = preg_split('/\s+/', $sor);
    $output = [];

    foreach ($szavak as $szo) {
        $tisztitott = mb_strtolower(preg_replace('/[^\p{L}0-9\-]/u', '', $szo));
        $role = '';

        if (in_array($tisztitott, $alanyok)) {
            $role = 'alany';
        } elseif (in_array($tisztitott, $allitmanyok)) {
            $role = 'allitmany';
        } elseif (in_array($tisztitott, $targyak)) {
            $role = 'targy';
        }

        $output[] = [
            'text' => $szo,
            'clean' => $tisztitott,
            'role' => $role
        ];
    }

    return $output;
}
