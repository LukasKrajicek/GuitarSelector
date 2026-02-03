<?php
session_start();
require_once 'db_connect.php';

// OCHRANA: Přístup má jen tvůj Gmail
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=nemate-pristup");
    exit;
}

$message = "";

// ZPRACOVÁNÍ FORMULÁŘE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. SBĚR DAT A ZÁKLADNÍ OČISTA (Validace před insertem)
    $vyrobce = (int)$_POST['vyrobce_id'];
    $model   = htmlspecialchars(trim($_POST['model']));
    $cena    = (int)$_POST['cena'];
    $kat     = (int)$_POST['kategorie_id'];
    $uroven  = (int)$_POST['uroven_id'];
    $styl    = htmlspecialchars(trim($_POST['styl']));
    $obrazek = htmlspecialchars(trim($_POST['obrazek']));

    // 2. KONTROLA, ZDA NEJSOU POLE PRÁZDNÁ NEBO CENA NESMYSLNÁ
    if (empty($model) || empty($styl) || $cena <= 0) {
        $message = "<p style='color:red; font-weight:bold;'>Chyba: Všechna pole musí být vyplněna a cena musí být vyšší než 0!</p>";
    } else {
        try {
            // 3. SAMOTNÝ INSERT
            $sql = "INSERT INTO produkty (vyrobce_id, model, cena, kategorie_id, uroven_id, styl, obrazek) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$vyrobce, $model, $cena, $kat, $uroven, $styl, $obrazek]);

            $message = "<p style='color:green; font-weight:bold;'>Produkt '$model' byl úspěšně přidán!</p>";
        } catch (PDOException $e) {
            $message = "<p style='color:red;'>Chyba v databázi: " . $e->getMessage() . "</p>";
        }
    }
}

include_once 'templates/header.php';
?>

    <div class="detail-sekce">
        <div class="detail-wrapper">
            <h1>Administrace systému</h1>
            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px;">
                <strong>Sekce:</strong>
                <a href="admin.php" style="color: #f1c40f; font-weight: bold;">+ Přidat produkt</a>
                <a href="admin_uzivatele.php" style="color: #444; text-decoration: none;">👥 Registrovaní uživatelé</a>
            </div>
            <p>Přidání nového produktu do nabídky</p>
            <hr style="margin: 20px 0;">

            <?php echo $message; ?>

            <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">

                <div>
                    <label style="display:block; font-weight:bold;">Výrobce:</label>
                    <select name="vyrobce_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <option value="">-- Vyber výrobce --</option>
                        <?php
                        $vyr = $pdo->query("SELECT id, nazev FROM vyrobci ORDER BY nazev");
                        while ($row = $vyr->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Model (název):</label>
                    <input type="text" name="model" required maxlength="100" placeholder="Např. Player Stratocaster" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Cena (Kč):</label>
                    <input type="number" name="cena" placeholder="Např. 15000" required min="1" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Kategorie:</label>
                    <select name="kategorie_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <?php
                        $kat = $pdo->query("SELECT id, nazev FROM kategorie ORDER BY id");
                        while ($row = $kat->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Pro úroveň:</label>
                    <select name="uroven_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                        <?php
                        $ur = $pdo->query("SELECT id, nazev FROM urovne ORDER BY id");
                        while ($row = $ur->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Hudební styl (vepiš ručně):</label>
                    <input type="text" name="styl" placeholder="Např. Rock, Metal, Blues..." required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <div>
                    <label style="display:block; font-weight:bold;">Název obrázku (např. kytara1.jpg):</label>
                    <input type="text" name="obrazek" placeholder="kytara.jpg" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ccc;">
                </div>

                <button type="submit" class="btn-vlozit" style="margin-top:10px;">Přidat produkt do katalogu</button>
            </form>

            <br>
            <a href="index.php" style="color: #666; text-decoration:none;">← Zpět na hlavní stránku</a>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>