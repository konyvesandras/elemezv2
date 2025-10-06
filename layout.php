<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szövegelemző – Mormon könyve</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/kijeloles.js" defer></script>
    <script src="assets/szotarazo.js" defer></script>
    <script src="assets/darkmode.js" defer></script>
    <script src="assets/frissites.js" defer></script>
    <script src="assets/speak.js" defer></script>

</head>
<body>

<h2>📘 Szövegelemzés - <?php echo $_GET['fajl'] ?></h2>

<!-- Szöveg megjelenítése -->
<div id="szoveg">
    <?php require_once 'render_szoveg.php'; ?>
</div>

<!-- Szótár frissítő gomb -->
<button id="szotarazo">🌗 Szótárazás</button>
<button id="frissites">🔄 Frissítés</button>
<button id="modvalto">🌗 Módváltás</button>

</body>
</html>
