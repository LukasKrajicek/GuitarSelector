<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['uzivatel_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['uzivatel_id'];
$p_id = (int)$_GET['id'];

try {
    // Kontrola, zda už produkt v oblíbených je
    $check = $pdo->prepare("SELECT id FROM oblibene WHERE uzivatel_id = ? AND produkt_id = ?");
    $check->execute([$u_id, $p_id]);

    if ($check->fetch()) {
        // Pokud existuje, pošleme červenou hlášku
        header("Location: detail.php?id=$p_id&msg=uz_existuje");
    } else {
        // Pokud neexistuje, uložíme a pošleme zelenou hlášku
        $sql = "INSERT INTO oblibene (uzivatel_id, produkt_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$u_id, $p_id]);
        header("Location: detail.php?id=$p_id&msg=ulozeno");
    }
} catch (PDOException $e) {
    die("Chyba: " . $e->getMessage());
}