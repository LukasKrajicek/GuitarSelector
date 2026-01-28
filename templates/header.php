<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuitarSelector</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<header class="top-bar">
    <div class="header-left">
        <button class="menu-btn" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="header-center">
        <a href="index.php" class="logo-text">GuitarSelector</a>
    </div>

    <nav class="header-right">
        <?php if (isset($_SESSION['uzivatel_id'])): ?>
            <span style="color: #f1c40f; margin-right: 15px; font-size: 0.9rem;">
                Ahoj, <?php echo htmlspecialchars($_SESSION['uzivatel_jmeno']); ?>
            </span>
            <a href="logout.php" style="color: white; text-decoration: none; font-size: 0.9rem;">Odhlásit</a>
        <?php else: ?>
            <a href="login.php" style="color: white; text-decoration: none; margin-right: 15px; font-size: 0.9rem;">Přihlásit</a>
            <a href="registrace.php" style="color: white; text-decoration: none; font-size: 0.9rem;">Registrace</a>
        <?php endif; ?>
    </nav>
</header>

<div id="side-menu" class="side-menu">
    <?php if (isset($_SESSION['uzivatel_id'])): ?>
        <a href="logout.php" style="font-weight: bold; border-bottom: 2px solid #444;">Odhlásit se (<?php echo htmlspecialchars($_SESSION['uzivatel_jmeno']); ?>)</a>
    <?php else: ?>
        <a href="login.php" style="font-weight: bold;">Přihlásit se</a>
    <?php endif; ?>
    <a href="profil.php">PROFIL</a>
    <a href="index.php">Domů</a>
    <a href="produkty.php?kat=1">Kytary</a>
    <a href="produkty.php?kat=2">Komba</a>
    <a href="dotaznik.php">Průvodce výběrem</a>

    <?php if (!isset($_SESSION['uzivatel_id'])): ?>
        <hr style="border: 0; border-top: 1px solid #333; margin: 5px 0;">
        <a href="registrace.php">Registrace</a>
    <?php endif; ?>
</div>

<script>
    function toggleMenu() {
        // Opravené ID na side-menu podle tvého zjištění
        var menu = document.getElementById('side-menu');
        if (menu) {
            menu.classList.toggle('active');
            console.log("Menu přepnuto!");
        } else {
            console.error("Chyba: Element side-menu nebyl nalezen!");
        }
    }
</script>