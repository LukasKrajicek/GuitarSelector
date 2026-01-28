<?php
session_start();
require_once 'db_connect.php';

if (isset($_SESSION['uzivatel_id']) && isset($_GET['id'])) {
    $vyber_id = (int)$_GET['id'];
    $uzivatel_id = $_SESSION['uzivatel_id'];

    // Smažeme záznam, ale jen pokud patří přihlášenému uživateli (bezpečnost!)
    $sql = "DELETE FROM ulozeny_vyber WHERE id = :vid AND uzivatel_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'vid' => $vyber_id,
        'uid' => $uzivatel_id
    ]);
}

header("Location: profil.php");
exit;
