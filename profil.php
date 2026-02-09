<?php
session_start();
require_once 'db_connect.php';

// Pokud není přihlášen, nepustíme ho sem
if (!isset($_SESSION['uzivatel_id'])) {
    header("Location: login.php?error=nemate-pristup");
    exit;
}

$uzivatel_id = $_SESSION['uzivatel_id'];

// 1. SQL: Načtení samostatných produktů (ty, které NEMAJÍ set_id)
$sql_solo = "SELECT p.*, v.nazev AS vyrobce_nazev, o.id AS oblibene_id, p.kategorie_id
             FROM oblibene o
             JOIN produkty p ON o.produkt_id = p.id
             JOIN vyrobci v ON p.vyrobce_id = v.id
             WHERE o.uzivatel_id = :uid AND o.set_id IS NULL
             ORDER BY o.datum_pridani DESC";

$stmt = $pdo->prepare($sql_solo);
$stmt->execute(['uid' => $uzivatel_id]);
$ulozeno = $stmt->fetchAll();

// 2. SQL: Načtení SETŮ (seskupené podle set_id)
$sql_sety = "SELECT p.*, v.nazev AS vyrobce_nazev, o.set_id, o.id AS oblibene_id
             FROM oblibene o
             JOIN produkty p ON o.produkt_id = p.id
             JOIN vyrobci v ON p.vyrobce_id = v.id
             WHERE o.uzivatel_id = :uid AND o.set_id IS NOT NULL
             ORDER BY o.set_id DESC";

$stmt_sety = $pdo->prepare($sql_sety);
$stmt_sety->execute(['uid' => $uzivatel_id]);
$vsechny_sety_raw = $stmt_sety->fetchAll();

// Pomocné pole pro seskupení setů podle jejich ID v PHP
$sety_skupiny = [];
foreach ($vsechny_sety_raw as $polozka) {
    $sety_skupiny[$polozka['set_id']][] = $polozka;
}

include_once 'templates/header.php';
?>

    <div class="produkty-sekce">
        <div class="container">
            <h1>Můj profil a uložený výběr</h1>
            <p style="margin-bottom: 30px;">Ahoj <strong><?php echo htmlspecialchars($_SESSION['uzivatel_jmeno'] ?? $_SESSION['uzivatel_email']); ?></strong>!</p>

            <h2 style="margin-top: 50px; border-bottom: 2px solid #27ae60; padding-bottom: 10px; color: #27ae60;">✨ Moje uložené sety sestavené na míru</h2>
            <div style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
                <?php if (count($sety_skupiny) > 0): ?>
                    <?php foreach ($sety_skupiny as $sid => $produkty_v_setu): ?>
                        <div style="background: #f0fdf4; border: 2px solid #27ae60; border-radius: 15px; padding: 20px; position: relative;">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <?php
                                $total_price = 0;
                                foreach ($produkty_v_setu as $p_set):
                                    $total_price += $p_set['cena'];
                                    ?>
                                    <div style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 10px; border: 1px solid #c3e6cb;">
                                        <img src="img/<?php echo htmlspecialchars($p_set['obrazek']); ?>" style="height: 80px; object-fit: contain;">
                                        <h4 style="font-size: 0.8rem; margin: 5px 0;"><?php echo htmlspecialchars($p_set['vyrobce_nazev'] . " " . $p_set['model']); ?></h4>
                                        <a href="detail.php?id=<?php echo $p_set['id']; ?>" style="font-size: 0.7rem; color: #27ae60;">Detail</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                                <strong style="color: #155724;">Celková cena setu: <?php echo number_format($total_price, 0, ',', ' '); ?> Kč</strong>
                                <a href="smazat_oblibene.php?set_id=<?php echo $sid; ?>"
                                   onclick="return confirm('Smazat celý set?')"
                                   style="color: #e74c3c; text-decoration: none; font-size: 0.8rem; font-weight: bold;">❌ Smazat set</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888;">Zatím nemáš uložený žádný set z průvodce.</p>
                <?php endif; ?>
            </div>

            <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

            <h2 style="margin-top: 40px; border-bottom: 2px solid #f1c40f; padding-bottom: 10px;">🎸 Samostatné uložené kytary</h2>
            <div class="produkty-grid" style="margin-top: 20px;">
                <?php
                $nasel_kytaru = false;
                foreach ($ulozeno as $p):
                    if ($p['kategorie_id'] == 1): $nasel_kytaru = true;
                        ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto" style="max-width:100%; height:150px; object-fit:contain;">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <div style="display: flex; gap: 10px; margin-top: auto;">
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail" style="flex: 1; text-align: center;">Detail</a>
                                <a href="smazat_oblibene.php?id=<?php echo $p['oblibene_id']; ?>"
                                   onclick="return confirm('Odebrat z výběru?')"
                                   style="background: #e74c3c; color: white; padding: 10px; border-radius: 6px; text-decoration: none;">🗑️</a>
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                <?php if (!$nasel_kytaru) echo "<p style='grid-column: 1 / -1; color: #888;'>Zatím žádné samostatné kytary.</p>"; ?>
            </div>

            <h2 style="margin-top: 50px; border-bottom: 2px solid #f1c40f; padding-bottom: 10px;">🔊 Samostatná uložená komba</h2>
            <div class="produkty-grid" style="margin-top: 20px;">
                <?php
                $nasel_kombo = false;
                foreach ($ulozeno as $p):
                    if ($p['kategorie_id'] == 2): $nasel_kombo = true;
                        ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto" style="max-width:100%; height:150px; object-fit:contain;">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">
                                <?php echo $p['vykon_w']; ?>W | <?php echo htmlspecialchars($p['technologie']); ?>
                            </p>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <div style="display: flex; gap: 10px; margin-top: auto;">
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail" style="flex: 1; text-align: center;">Detail</a>
                                <a href="smazat_oblibene.php?id=<?php echo $p['oblibene_id']; ?>"
                                   onclick="return confirm('Odebrat z výběru?')"
                                   style="background: #e74c3c; color: white; padding: 10px; border-radius: 6px; text-decoration: none;">🗑️</a>
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                <?php if (!$nasel_kombo) echo "<p style='grid-column: 1 / -1; color: #888;'>Zatím žádná samostatná komba.</p>"; ?>
            </div>

            <div style="margin-top: 50px; border-top: 1px solid #ddd; padding-top: 20px;">
                <a href="logout.php" style="color: #e74c3c; text-decoration: none; font-weight: bold;">Odhlásit se z účtu</a>
            </div>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>