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
            <img src="img/logo.png" alt="Logo" style="height: 40px; vertical-align: middle;">
            <span class="logo-text">GuitarSelector</span>
        </a>
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

    <div id="flash-container" style="position: fixed; top: 70px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 90%; max-width: 400px;">
        <?php if (isset($_GET['login']) && $_GET['login'] == 'success'): ?>
            <div class="flash-message" style="background: #27ae60; color: white; text-align: center; padding: 15px; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 10px;">
                Vítejte zpět! 👋 Přihlášení proběhlo úspěšně.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'registrovan'): ?>
            <div class="flash-message" style="background: #27ae60; color: white; text-align: center; padding: 15px; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 10px;">
                Registrace byla úspěšná! 🎉 Nyní se můžete přihlásit.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'smazano'): ?>
            <div class="flash-message" style="background: #e67e22; color: white; text-align: center; padding: 15px; border-radius: 8px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 10px;">
                Odstraněno z databáze. 🗑️
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Najde všechny hlášky a po 3 sekundách je nechá zmizet
        setTimeout(function() {
            const messages = document.querySelectorAll('.flash-message');
            messages.forEach(msg => {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500); // Po animaci úplně smaže z HTML
            });
        }, 3000);
    </script>

</header>

<div id="side-menu" class="side-menu">
    <?php if (isset($_SESSION['uzivatel_id'])): ?>
        <a href="logout.php" style="font-weight: bold; border-bottom: 2px solid #444;">Odhlásit se</a>
        <a href="profil.php" style="color: #f1c40f;">Můj profil (Uložené)</a> <?php else: ?>
        <a href="login.php" style="font-weight: bold;">Přihlásit se</a>
    <?php endif; ?>

    <a href="index.php">Domů</a>
    <a href="produkty.php?kat=1">Kytary</a>
    <a href="produkty.php?kat=2">Komba</a>
    <a href="dotaznik.php">Průvodce výběrem</a>
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