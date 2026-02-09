<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['uzivatel_id']) || !isset($_GET['k_id']) || !isset($_GET['a_id'])) {
    header("Location: dotaznik.php");
    exit;
}

$u_id = $_SESSION['uzivatel_id'];
$k_id = (int)$_GET['k_id'];
$a_id = (int)$_GET['a_id'];
$set_kod = "SET_" . time() . "_" . $u_id; // Unikátní klíč pro tento pár

try {
    $stmt = $pdo->prepare("INSERT INTO oblibene (uzivatel_id, produkt_id, set_id) VALUES (?, ?, ?), (?, ?, ?)");
    $stmt->execute([$u_id, $k_id, $set_kod, $u_id, $a_id, $set_kod]);

    header("Location: profil.php?msg=set_ulozen");
} catch (PDOException $e) {
    die("Chyba: " . $e->getMessage());
}
