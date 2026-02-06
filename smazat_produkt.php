<?php
session_start();
require_once 'db_connect.php';

// 1. OCHRANA: Přístup má jen tvůj Gmail (stejně jako v adminu)
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=nema-opravneni");
    exit;
}

// 2. KONTROLA: Máme ID produktu, který chceme smazat?
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Převedeme na číslo pro jistotu

    try {
        $stmt = $pdo->prepare("DELETE FROM produkty WHERE id = ?");
        $stmt->execute([$id]);

        // Přesměrujeme zpět s potvrzením o smazání
        header("Location: admin_produkty.php?msg=smazano");
        exit;
    } catch (PDOException $e) {
        // Pokud by to nešlo smazat (třeba kvůli cizím klíčům), vypíšeme chybu
        die("Chyba při mazání: " . $e->getMessage());
    }
} else {
    // Pokud někdo vleze na soubor přímo bez ID
    header("Location: admin_produkty.php");
    exit;
}