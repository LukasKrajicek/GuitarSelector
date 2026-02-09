<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['uzivatel_id'])) exit;

$u_id = $_SESSION['uzivatel_id'];

if (isset($_GET['set_id'])) {
    // Mažeme celý set
    $stmt = $pdo->prepare("DELETE FROM oblibene WHERE set_id = ? AND uzivatel_id = ?");
    $stmt->execute([$_GET['set_id'], $u_id]);
} elseif (isset($_GET['id'])) {
    // Mažeme jeden produkt
    $stmt = $pdo->prepare("DELETE FROM oblibene WHERE id = ? AND uzivatel_id = ?");
    $stmt->execute([(int)$_GET['id'], $u_id]);
}

header("Location: profil.php?msg=odebrano");