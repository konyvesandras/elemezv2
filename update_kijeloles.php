<?php
// JSON POST beolvasása
$input = json_decode(file_get_contents('php://input'), true);
$word = trim($input['word'] ?? '');
$action = $input['action'] ?? ''; // 'add' vagy 'remove'

// Érvényesség ellenőrzése
if ($word === '' || !in_array($action, ['add', 'remove'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Hibás kérés']);
    exit;
}

// Kiemeltek betöltése
$file = 'kiemeltek.json';
$kiemeltek = file_exists($file)
    ? json_decode(file_get_contents($file), true)
    : [];

if (!is_array($kiemeltek)) $kiemeltek = [];

// Módosítás
if ($action === 'add' && !in_array($word, $kiemeltek)) {
    $kiemeltek[] = $word;
}
if ($action === 'remove') {
    $kiemeltek = array_values(array_filter($kiemeltek, fn($w) => $w !== $word));
}

// Mentés
file_put_contents($file, json_encode($kiemeltek, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['message' => '✅ Frissítve']);
