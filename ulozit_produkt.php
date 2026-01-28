<?php
session_start();
require_once 'db_connect.php';

// Kontrola, zda je uživatel přihlášen a zda máme ID produktu
if (!isset($_SESSION['uzivatel_id']) || !isset($_GET['id'])) {
    header("Location: login.php?error=pro-ulozeni-se-prihlaste");
    exit;
}

$uzivatel_id = $_SESSION['uzivatel_id'];
$produkt_id = (int)$_GET['id'];

// 1. Zkontrolujeme, zda produkt v seznamu už neexistuje
$checkSql = "SELECT id FROM ulozeny_vyber WHERE uzivatel_id = :uid AND produkt_id = :pid";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['uid' => $uzivatel_id, 'pid' => $produkt_id]);

if (!$checkStmt->fetch()) {
    // 2. Pokud tam není, vložíme ho tam
    $insertSql = "INSERT INTO ulozeny_vyber (uzivatel_id, produkt_id) VALUES (:uid, :pid)";
    $pdo->prepare($insertSql)->execute(['uid' => $uzivatel_id, 'pid' => $produkt_id]);
}

// 3. Přesměrujeme na profil, kde uvidí svůj seznam
header("Location: profil.php");
exit;