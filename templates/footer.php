</main>
<footer>
    <p>&copy; <?php echo date("Y"); ?> GuitarSelector - Maturitní projekt</p>

    <?php if (isset($_SESSION['uzivatel_email']) && $_SESSION['uzivatel_email'] === 'lukass.krajicek@gmail.com'): ?>
        <a href="admin.php" style="color: #444; font-size: 0.7rem; text-decoration: none; margin-top: 10px; display: inline-block;">
            Administrace systému
        </a>
    <?php endif; ?>
</footer>

<script>
    function toggleMenu() {
        const menu = document.getElementById("side-menu");
        // Přidá nebo odebere třídu 'active', která v CSS ovládá pozici 'left'
        menu.classList.toggle("active");
    }

    // Zavření menu, když uživatel klikne kamkoliv jinam na stránku
    document.addEventListener('click', function(event) {
        const menu = document.getElementById("side-menu");
        const btn = document.querySelector('.menu-btn');

        // Pokud kliknutí nebylo na menu ani na tlačítko a menu je otevřené, zavři ho
        if (!menu.contains(event.target) && !btn.contains(event.target)) {
            menu.classList.remove("active");
        }
    });
</script>
</body>
</html>