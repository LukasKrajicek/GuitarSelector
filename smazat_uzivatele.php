<?php
session_start();
require_once 'db_connect.php';

// OCHRANA: Přístup má jen tvůj Gmail
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=nepristupno");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Získáme e-mail uživatele, abychom náhodou nesmazali admina přes přímé URL
    $stmt = $pdo->prepare("SELECT email FROM uzivatele WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && $user['email'] !== 'lukass.krajicek@gmail.com') {
        $delete = $pdo->prepare("DELETE FROM uzivatele WHERE id = ?");
        $delete->execute([$id]);
        header("Location: admin_uzivatele.php?msg=smazano");
        exit;
    }
}

header("Location: admin_uzivatele.php");
exit;