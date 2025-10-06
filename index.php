<?php
$konyvtar = 'txt/';
$fajlok = glob($konyvtar . '*.txt');
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Szövegelemző – Fájlválasztás</title>
  <style>
    body { font-family: sans-serif; padding: 2em; background: #f9f9f9; }
    ul { list-style: none; padding: 0; }
    li { margin: 0.5em 0; }
    a { text-decoration: none; color: #0077cc; font-weight: bold; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h1>📄 Válassz egy szövegfájlt elemzéshez</h1>
  <ul>
    <?php foreach ($fajlok as $fajl): 
      $nev = basename($fajl); ?>
      <li><a href="szovegelemzo.php?fajl=<?php echo urlencode($nev); ?>"><?php echo htmlspecialchars($nev); ?></a></li>
    <?php endforeach; ?>
  </ul>
<p>
  <a href="https://www.margitszigetijoga.hu/dropbox/zip/zip/php%20-%20szoveg%20elemezes%20es%20szotarazas.zip" class="zip-download" download>
    📦 Letöltés: Szövegelemző + Szótárazás ZIP
  </a>
</p>

</body>
</html>
