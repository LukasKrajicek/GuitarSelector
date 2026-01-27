<?php
session_start();
if (!isset($_SESSION['uzivatel_id'])) {
    // Pokud v session není ID uživatele, přesměrujeme na login
    header("Location: login.php?error=pro-dotaznik-se-musite-prihlasit");
    exit;
}
require_once 'db_connect.php';
include_once 'templates/header.php';
?>

<div class="detail-sekce">
    <div class="detail-wrapper">
        <span class="kategorie-tag">Interaktivní průvodce</span>
        <h1>Najdi svůj ideální nástroj</h1>
        <p style="margin-bottom: 25px; color: #666;">Odpověz na 3 otázky a my ti doporučíme to nejlepší pro tvůj start nebo posun v hraní.</p>

        <form action="vysledek.php" method="GET">
            <div style="margin-bottom: 25px;">
                <h3 style="margin-bottom: 10px;">1. Co si dnes chceš vybrat?</h3>
                <div style="display: flex; gap: 15px;">
                    <label style="flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; text-align: center;">
                        <input type="radio" name="kat" value="1" checked style="margin-right: 5px;"> Kytara
                    </label>
                    <label style="flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; text-align: center;">
                        <input type="radio" name="kat" value="2" style="margin-right: 5px;"> Kombo
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <h3 style="margin-bottom: 10px;">2. Jak jsi na tom s hraním?</h3>
                <select name="uroven" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; font-size: 1rem;">
                    <option value="1">Jsem začátečník (hledám první kousek)</option>
                    <option value="2">Jsem pokročilý (hraju už nějakou dobu)</option>
                    <option value="3">Jsem profík (hledám top kvalitu)</option>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 10px;">3. Jaký je tvůj maximální rozpočet?</h3>
                <div style="position: relative;">
                    <input type="number" name="cena_max" placeholder="např. 15000" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; padding-right: 40px;">
                    <span style="position: absolute; right: 15px; top: 12px; color: #888;">Kč</span>
                </div>
            </div>

            <button type="submit" class="btn-vlozit">Ukázat mi nejlepší výsledky</button>
        </form>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
