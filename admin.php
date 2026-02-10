<?php
session_start();
require_once 'db_connect.php';

// Ochrana administrace
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=nemate-pristup");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vyrobce_id  = (int)$_POST['vyrobce_id'];
    $model       = htmlspecialchars(trim($_POST['model']));
    $cena        = (int)$_POST['cena'];
    $kat_id      = (int)$_POST['kategorie_id'];
    $uroven_id   = (int)$_POST['uroven_id'];
    $styl_id     = (int)$_POST['styl_id'];
    $obrazek     = htmlspecialchars(trim($_POST['obrazek']));

    if ($kat_id == 2) {
        $technologie = !empty($_POST['technologie']) ? $_POST['technologie'] : null;
        $vykon       = !empty($_POST['vykon']) ? (int)$_POST['vykon'] : null;
    } else {
        $technologie = null;
        $vykon       = null;
    }

    try {
        $sql = "INSERT INTO produkty (vyrobce_id, model, cena, kategorie_id, uroven_id, styl_id, obrazek, technologie, vykon_w) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vyrobce_id, $model, $cena, $kat_id, $uroven_id, $styl_id, $obrazek, $technologie, $vykon]);
        $message = "<div class='flash-message success' style='display:block; opacity:1;'>Produkt '$model' úspěšně přidán!</div>";
    } catch (PDOException $e) {
        $message = "<div class='flash-message warning' style='display:block; opacity:1;'>Chyba: " . $e->getMessage() . "</div>";
    }
}

include_once 'templates/header.php';
?>

    <section class="admin-sekce">
        <div class="container">

            <div class="quiz-card" style="margin-bottom: 30px; padding: 20px;">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                    <h2 style="margin:0; font-size: 1.2rem; border-right: 2px solid #eee; padding-right: 20px;">🛠️ Administrace</h2>
                    <a href="admin.php" class="feature-link" style="color: var(--main-yellow);">+ Přidat produkt</a>
                    <a href="admin_produkty.php" style="text-decoration:none; color: var(--text-muted);">🎸 Správa produktů</a>
                    <a href="admin_uzivatele.php" style="text-decoration:none; color: var(--text-muted);">👥 Registrovaní uživatelé</a>
                </div>
            </div>

            <div class="quiz-card" style="max-width: 800px; margin: 0 auto;">
                <span class="badge">Nový záznam</span>
                <h1 style="margin-bottom: 10px;">Přidat produkt</h1>
                <p style="color: var(--text-muted); margin-bottom: 30px;">Vyplňte údaje pro zařazení nástroje nebo aparátu do katalogu.</p>

                <?php echo $message; ?>

                <form method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Typ produktu (Kategorie)</label>
                            <select name="kategorie_id" id="kat_select" class="form-control" required>
                                <option value="">-- Vyber typ --</option>
                                <?php
                                $kat_list = $pdo->query("SELECT id, nazev FROM kategorie ORDER BY id");
                                while ($row = $kat_list->fetch()) {
                                    echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Výrobce</label>
                            <select name="vyrobce_id" class="form-control" required>
                                <option value="">-- Vyber výrobce --</option>
                                <?php
                                $vyr = $pdo->query("SELECT id, nazev FROM vyrobci ORDER BY nazev");
                                while ($row = $vyr->fetch()) {
                                    echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Model (název)</label>
                        <input type="text" name="model" class="form-control" required placeholder="Např. Player Stratocaster">
                    </div>

                    <div class="form-group">
                        <label>Cena (Kč)</label>
                        <input type="number" name="cena" class="form-control" placeholder="Např. 15000" required min="1">
                    </div>

                    <div id="kombo_pole" style="display: none; background: #fffdf0; padding: 20px; border-radius: 12px; border: 2px solid var(--main-yellow); margin: 20px 0; gap: 20px;">
                        <div style="flex: 1;">
                            <label style="font-weight:bold;">Technologie:</label>
                            <select name="technologie" class="form-control">
                                <option value="Tranzistor">Tranzistor</option>
                                <option value="Lampa">Lampa</option>
                                <option value="Modelace">Modelace (Digitál)</option>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label style="font-weight:bold;">Výkon (W):</label>
                            <input type="number" name="vykon" class="form-control" placeholder="Např. 50">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Pro úroveň</label>
                            <select name="uroven_id" class="form-control" required>
                                <?php
                                $ur = $pdo->query("SELECT id, nazev FROM urovne ORDER BY id");
                                while ($row = $ur->fetch()) {
                                    echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Hudební styl</label>
                            <select name="styl_id" class="form-control" required>
                                <option value="">-- Vyber styl --</option>
                                <?php
                                $styly = $pdo->query("SELECT id, nazev FROM styly ORDER BY nazev");
                                while ($row = $styly->fetch()) {
                                    echo "<option value='{$row['id']}'>{$row['nazev']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Název souboru obrázku</label>
                        <input type="text" name="obrazek" class="form-control" placeholder="např. fender_strat.jpg" required>
                    </div>

                    <button type="submit" class="btn-submit" style="width: 100%; margin-top: 20px;">PŘIDAT PRODUKT DO KATALOGU</button>
                </form>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="index.php" style="color: var(--text-muted); text-decoration:none;">← Zpět na domovskou stránku</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        const katSelect = document.getElementById('kat_select');
        const komboPole = document.getElementById('kombo_pole');

        katSelect.addEventListener('change', function() {
            if (this.value === '2') {
                komboPole.style.display = 'flex';
            } else {
                komboPole.style.display = 'none';
            }
        });
    </script>

<?php include_once 'templates/footer.php'; ?>