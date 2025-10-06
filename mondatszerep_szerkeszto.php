<?php
$json_path = __DIR__ . '/json/mondatszerepek.json';

// Mentés POST-tal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uj = [
        'alanyok' => array_map('trim', explode("\n", $_POST['alanyok'] ?? '')),
        'allitmanyok' => array_map('trim', explode("\n", $_POST['allitmanyok'] ?? '')),
        'targyak' => array_map('trim', explode("\n", $_POST['targyak'] ?? ''))
    ];
    file_put_contents($json_path, json_encode($uj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "<p style='color:green;'>✅ Mentve!</p>";
}

// Betöltés
$szerepek = file_exists($json_path)
    ? json_decode(file_get_contents($json_path), true)
    : ['alanyok'=>[], 'allitmanyok'=>[], 'targyak'=>[]];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>🧠 Mondatszerepek szerkesztése</title>
  <style>
    body { font-family: sans-serif; padding: 2em; background: #f9f9f9; }
    textarea { width: 100%; height: 8em; margin-bottom: 1em; font-family: monospace; }
    label { font-weight: bold; display: block; margin-top: 1em; }
    button { padding: 0.5em 1em; font-weight: bold; background: #0077cc; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
    button:hover { background: #005fa3; }
  </style>
</head>
<body>
  <h1>🧠 Mondatszerepek szerkesztése</h1>
  <form method="post">
    <label for="alanyok">👤 Alanyok (egy sor = egy szó)</label>
    <textarea name="alanyok"><?php echo implode("\n", $szerepek['alanyok'] ?? []); ?></textarea>

    <label for="allitmanyok">🗣️ Állítmányok</label>
    <textarea name="allitmanyok"><?php echo implode("\n", $szerepek['allitmanyok'] ?? []); ?></textarea>

    <label for="targyak">📦 Tárgyak</label>
    <textarea name="targyak"><?php echo implode("\n", $szerepek['targyak'] ?? []); ?></textarea>

    <button type="submit">💾 Mentés</button>
  </form>
</body>
</html>
