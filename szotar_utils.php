<?php
// 🧠 Nyelvfelismerés Google Translate API-val
function detectLanguage($text) {
    $url = "https://translate.googleapis.com/translate_a/single";
    $params = [
        'client' => 'gtx',
        'sl'     => 'auto',   // automatikus felismerés
        'tl'     => 'en',     // célnyelv: angol (csak a detektáláshoz)
        'dt'     => 't',
        'q'      => $text
    ];
    $response = @file_get_contents($url . '?' . http_build_query($params));
    $result = json_decode($response, true);

    // Google által detektált nyelv (pl. 'hu', 'en', 'hi', 'ne', 'sa')
    $detected = isset($result[2]) ? strtolower($result[2]) : 'hu';

    // 🛠️ Normalizálás: ha nepálinak (ne) vagy hindinek (hi) jelöli,
    // de a szöveg dévanágari írású, akkor kezeljük szanszkritként (sa).
    if (in_array($detected, ['ne', 'hi'])) {
        if (preg_match('/\p{Devanagari}/u', $text)) {
            $detected = 'sa'; // szanszkritként kezeljük
        }
    }

    // Ha valami teljesen ismeretlen, essünk vissza magyarra
    if (!preg_match('/^[a-z]{2}$/', $detected)) {
        $detected = 'hu';
    }

    return $detected;
}

// 🧪 Segédfüggvény: informatív-e a fordítás?
// Csak akkor fogadjuk el, ha nem üres és nem azonos az eredetivel
function isInformativeTranslation($src, $dst) {
    if (trim($dst) === '') return false;
    $a = mb_strtolower(trim($src), 'UTF-8');
    $b = mb_strtolower(trim($dst), 'UTF-8');
    return $a !== $b;
}

// 🌐 Egyszeri fordítás: adott forrás → cél
function googleTranslateOnce($text, $source, $target) {
    $url = "https://translate.googleapis.com/translate_a/single";
    $params = [
        'client' => 'gtx',
        'sl'     => $source,
        'tl'     => $target,
        'dt'     => 't',
        'q'      => $text
    ];
    $response = @file_get_contents($url . '?' . http_build_query($params));
    $result = json_decode($response, true);
    if (!is_array($result) || !isset($result[0])) return '';
    $out = '';
    foreach ($result[0] as $chunk) {
        $out .= $chunk[0] ?? '';
    }
    return trim($out);
}

// 🌐 Tömeges fordítás – okos változat
// Ha dévanágari szót kap, több forrásnyelvvel próbálkozik (sa, hi, ne)
function googleTranslateBatchSmart($lines, $sourceDetected, $target) {
    if (empty($lines)) return [];

    // 🔀 egyszerre több sort küldünk
    $text = implode("\n", $lines);
    $sources = [$sourceDetected];

    // Ha dévanágari van benne, próbáljuk több forrással
    if (preg_match('/\p{Devanagari}/u', $text)) {
        $sources = ['sa', 'hi', 'ne'];
    }

    foreach ($sources as $sl) {
        $url = "https://translate.googleapis.com/translate_a/single"
             . "?client=gtx&sl={$sl}&tl={$target}&dt=t&q=" . rawurlencode($text);
        $response = @file_get_contents($url);
        $result = json_decode($response, true);

        if (is_array($result) && isset($result[0])) {
            $full = '';
            foreach ($result[0] as $item) {
                $full .= $item[0] ?? '';
            }
            $translations = array_map('trim', explode("\n", $full));

            // Ellenőrizzük, hogy van‑e informatív fordítás
            $ok = false;
            foreach ($translations as $i => $t) {
                if (isInformativeTranslation($lines[$i], $t)) {
                    $ok = true; break;
                }
            }
            if ($ok) return $translations;
        }
    }

    // Ha semmi nem jött vissza
    return array_fill(0, count($lines), '');
}

// 📊 Állapotfájl frissítése (pl. progress barhoz)
function updateStatusFile($statusFile, $srcLang, $targetLang, $original, $translated, $progress, $history) {
    $status = [
        'srcLang'    => $srcLang,
        'targetLang' => $targetLang,
        'original'   => $original,
        'translated' => $translated,
        'progress'   => $progress,
        'history'    => $history
    ];
    file_put_contents($statusFile, json_encode($status, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}


// 🔤 Devanágari → latin (IAST-szerű) átírás
function transliterateDevanagari($text) {
    $map = [
        // Magánhangzók
        'अ'=>'a','आ'=>'ā','इ'=>'i','ई'=>'ī','उ'=>'u','ऊ'=>'ū',
        'ऋ'=>'ṛ','ॠ'=>'ṝ','ऌ'=>'ḷ','ॡ'=>'ḹ',
        'ए'=>'e','ऐ'=>'ai','ओ'=>'o','औ'=>'au',

        // Mássalhangzók
        'क'=>'ka','ख'=>'kha','ग'=>'ga','घ'=>'gha','ङ'=>'ṅa',
        'च'=>'ca','छ'=>'cha','ज'=>'ja','झ'=>'jha','ञ'=>'ña',
        'ट'=>'ṭa','ठ'=>'ṭha','ड'=>'ḍa','ढ'=>'ḍha','ण'=>'ṇa',
        'त'=>'ta','थ'=>'tha','द'=>'da','ध'=>'dha','न'=>'na',
        'प'=>'pa','फ'=>'pha','ब'=>'ba','भ'=>'bha','म'=>'ma',
        'य'=>'ya','र'=>'ra','ल'=>'la','व'=>'va',
        'श'=>'śa','ष'=>'ṣa','स'=>'sa','ह'=>'ha',

        // Ligatúrák és speciális jelek
        'ं'=>'ṃ','ः'=>'ḥ','ँ'=>'̃','ऽ'=>"’",
        '्'=>'', // halant: elnyeli a magánhangzót

        // Számok
        '०'=>'0','१'=>'1','२'=>'2','३'=>'3','४'=>'4',
        '५'=>'5','६'=>'6','७'=>'7','८'=>'8','९'=>'9',
    ];

    // Karakterenként cseréljük
    $out = '';
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($chars as $ch) {
        $out .= $map[$ch] ?? $ch; // ha nincs mapping, hagyjuk eredetiben
    }

    // 🧹 Utólagos tisztítás: felesleges "a" a szóvégekről (pl. "vadantia" → "vadanti")
    $out = preg_replace('/a(\s|$)/u', '$1', $out);

    return $out;
}
