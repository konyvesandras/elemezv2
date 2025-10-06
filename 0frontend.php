<?php
$szoveg = file_get_contents('elemzes.txt');
$kiemeltek = file_exists('kiemeltek.json')
    ? json_decode(file_get_contents('kiemeltek.json'), true)
    : [];

$szavak = preg_split('/\s+/', $szoveg);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szövegelemző – Mormon könyve</title>
    <style>
        body { font-family: sans-serif; padding: 2em; background: #f9f9f9; }
        .word { cursor: pointer; padding: 2px 4px; margin: 1px; border-radius: 4px; }
        .selected { background-color: #ffe08a; }
        #szotarazo { margin-top: 1em; padding: 0.5em 1em; font-size: 1em; }
    </style>
<script src="assets/kijeloles.js"></script>
<script src="assets/szotarazo.js"></script>

</head>
<body>

<h2>📘 Szövegelemzés – Mormon könyve</h2>

<div id="szoveg">
<?php
foreach ($szavak as $szo) {
    $tisztitott = trim($szo, ".,;:!?()[]\"");
    $class = in_array($tisztitott, $kiemeltek) ? 'word selected' : 'word';
    echo "<span class='$class'>$tisztitott</span> ";
}
?>
</div>

<button id="szotarazo">🌗 Szótár frissítés</button>

<script>
const selectedWords = new Set();

// 🔁 Visszatöltés: kijelölt szavak betöltése
document.querySelectorAll('.word').forEach(el => {
    const word = el.textContent.trim();
    if (el.classList.contains('selected')) {
        selectedWords.add(word);
    }

    el.addEventListener('click', () => {
        el.classList.toggle('selected');
        if (selectedWords.has(word)) {
            selectedWords.delete(word);
        } else {
            selectedWords.add(word);
        }
    });
});

document.getElementById('szotarazo').addEventListener('click', () => {
    fetch('backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ words: Array.from(selectedWords) })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Szótár frissítve!');
    });
});
</script>

</body>
</html>
