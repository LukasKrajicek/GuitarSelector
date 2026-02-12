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
    <link rel="icon" type="image/png" href="img/favicon.png">
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
        <a href="index.php" class="logo-link">
            <img src="img/logo.png" alt="Logo" class="logo-img">
            <span class="logo-text">Guitar<span>Selector</span></span>
        </a>
    </div>

    <nav class="header-right">
        <?php if (isset($_SESSION['uzivatel_id'])): ?>
            <div class="user-info">
                <span class="welcome-text">Ahoj, <?php echo htmlspecialchars($_SESSION['uzivatel_jmeno']); ?></span>
                <a href="logout.php" class="auth-link logout">Odhlásit</a>
            </div>
        <?php else: ?>
            <a href="login.php" class="auth-link">Přihlásit</a>
            <a href="registrace.php" class="auth-link register">Registrace</a>
        <?php endif; ?>
    </nav>

    <div id="flash-container">
        <?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
            <div class="flash-message success">Vítejte zpět! 👋 Přihlášení proběhlo úspěšně.</div>
        <?php endif; ?>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'registrovan'): ?>
            <div class="flash-message success">Registrace byla úspěšná! 🎉 Nyní se se přihlas.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'smazano'): ?>
            <div class="flash-message warning">Odstraněno z databáze. 🗑️</div>
        <?php endif; ?>
    </div>
</header>

<div id="side-menu" class="side-menu">
    <div class="side-menu-header">
        <?php if (isset($_SESSION['uzivatel_id'])): ?>
            <p>Přihlášen jako: <strong><?php echo htmlspecialchars($_SESSION['uzivatel_jmeno']); ?></strong></p>
        <?php endif; ?>
    </div>
    <a href="index.php">🏠 Domů</a>
    <a href="produkty.php?kat=1">🎸 Kytary</a>
    <a href="produkty.php?kat=2">🔊 Komba</a>
    <a href="dotaznik.php">✨ Průvodce výběrem</a>
    <?php if (isset($_SESSION['uzivatel_id'])): ?>
        <hr>
        <a href="profil.php">👤 Můj profil</a>
        <a href="logout.php" class="logout-side">Odhlásit se</a>
    <?php else: ?>
        <hr>
        <a href="login.php">🔑 Přihlásit se</a>
    <?php endif; ?>
</div>

<script>
    function toggleMenu() {
        var menu = document.getElementById('side-menu');
        if (menu) menu.classList.toggle('active');
    }

    // Auto-hide flash zpráv
    setTimeout(function() {
        const messages = document.querySelectorAll('.flash-message');
        messages.forEach(msg => {
            msg.style.opacity = "0";
            setTimeout(() => msg.remove(), 500);
        });
    }, 3000);
</script>