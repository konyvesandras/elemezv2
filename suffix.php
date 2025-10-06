<?php
function betoltott_suffixek() {
  $nyelvek = ['hu', 'en', 'sa', 'hi'];
  $osszes = [];

  foreach ($nyelvek as $lang) {
    $fajl = __DIR__ . "/suffixek/$lang.php";
    if (file_exists($fajl)) {
      $suffixek = include $fajl;
      foreach ($suffixek as $veg => $info) {
        $osszes[$veg] = array_merge(['lang' => $lang], $info);
      }
    }
  }

  return $osszes;
}
