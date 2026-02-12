<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['uzivatel_id'])) {
    header("Location: login.php?error=nemate-pristup");
    exit;
}

$uzivatel_id = $_SESSION['uzivatel_id'];

// 1. SQL: Načtení samostatných produktů
$sql_solo = "SELECT p.*, v.nazev AS vyrobce_nazev, o.id AS oblibene_id, p.kategorie_id
             FROM oblibene o
             JOIN produkty p ON o.produkt_id = p.id
             JOIN vyrobci v ON p.vyrobce_id = v.id
             WHERE o.uzivatel_id = :uid AND o.set_id IS NULL
             ORDER BY o.datum_pridani DESC";

$stmt = $pdo->prepare($sql_solo);
$stmt->execute(['uid' => $uzivatel_id]);
$ulozeno = $stmt->fetchAll();

// 2. SQL: Načtení SETŮ
$sql_sety = "SELECT p.*, v.nazev AS vyrobce_nazev, o.set_id, o.id AS oblibene_id
             FROM oblibene o
             JOIN produkty p ON o.produkt_id = p.id
             JOIN vyrobci v ON p.vyrobce_id = v.id
             WHERE o.uzivatel_id = :uid AND o.set_id IS NOT NULL
             ORDER BY o.set_id DESC";

$stmt_sety = $pdo->prepare($sql_sety);
$stmt_sety->execute(['uid' => $uzivatel_id]);
$vsechny_sety_raw = $stmt_sety->fetchAll();

$sety_skupiny = [];
foreach ($vsechny_sety_raw as $polozka) {
    $sety_skupiny[$polozka['set_id']][] = $polozka;
}

include_once 'templates/header.php';
?>

    <section class="container" style="padding-top: 40px;">
        <div class="quiz-card" style="max-width: 100%; border-top: 10px solid var(--dark-blue);">
            <h1>Můj profil a uložený výběr</h1>
            <p>Ahoj, <strong><?php echo htmlspecialchars($_SESSION['uzivatel_jmeno'] ?? $_SESSION['uzivatel_email']); ?></strong>! Zde najdeš své uložené kousky.</p>

            <h2 style="margin-top: 40px; color: #27ae60; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem;">✨</span> Moje uložené sety
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 20px;">
                <?php if (count($sety_skupiny) > 0): ?>
                    <?php foreach ($sety_skupiny as $sid => $produkty_v_setu): ?>
                        <div style="background: #f0fdf4; border: 2px solid #27ae60; border-radius: 20px; padding: 20px; box-shadow: var(--shadow-soft);">
                            <div style="display: flex; gap: 10px;">
                                <?php
                                $total_price = 0;
                                foreach ($produkty_v_setu as $p_set):
                                    $total_price += $p_set['cena'];
                                    ?>
                                    <div style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 12px; border: 1px solid #c3e6cb;">
                                        <img src="img/<?php echo htmlspecialchars($p_set['obrazek']); ?>" style="height: 60px; width: 100%; object-fit: contain;">
                                        <h4 style="font-size: 0.7rem; margin: 5px 0; height: 30px; overflow: hidden;"><?php echo htmlspecialchars($p_set['vyrobce_nazev'] . " " . $p_set['model']); ?></h4>
                                        <a href="detail.php?id=<?php echo $p_set['id']; ?>" style="font-size: 0.7rem; color: #27ae60; font-weight: bold; text-decoration: none;">Detail</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #c3e6cb; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <small style="display:block; color: #666;">Cena za komplet:</small>
                                    <strong style="color: #155724; font-size: 1.1rem;"><?php echo number_format($total_price, 0, ',', ' '); ?> Kč</strong>
                                </div>
                                <a href="smazat_oblibene.php?set_id=<?php echo $sid; ?>"
                                   onclick="return confirm('Smazat celý set?')"
                                   style="color: #e74c3c; text-decoration: none; font-size: 0.8rem; font-weight: bold; padding: 8px 12px; background: #fff; border-radius: 8px; border: 1px solid #e74c3c;">🗑️ Smazat</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #888; grid-column: 1/-1;">Zatím nemáš uložený žádný set z průvodce.</p>
                <?php endif; ?>
            </div>

            <hr style="margin: 50px 0; opacity: 0.1;">

            <h2 style="margin-bottom: 25px;">🎸 Samostatné kytary</h2>
            <div class="produkty-grid">
                <?php
                $nasel_kytaru = false;
                foreach ($ulozeno as $p):
                    if ($p['kategorie_id'] == 1): $nasel_kytaru = true;
                        ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail" style="flex: 3;">Detail</a>
                                <a href="smazat_oblibene.php?id=<?php echo $p['oblibene_id']; ?>"
                                   onclick="return confirm('Odebrat z výběru?')"
                                   style="flex: 1; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; font-weight: bold;">🗑️</a>
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                <?php if (!$nasel_kytaru) echo "<p style='grid-column: 1 / -1; color: #888; text-align: center; padding: 40px;'>Zatím žádné samostatné kytary.</p>"; ?>
            </div>

            <h2 style="margin-top: 50px; margin-bottom: 25px;">🔊 Samostatná komba</h2>
            <div class="produkty-grid">
                <?php
                $nasel_kombo = false;
                foreach ($ulozeno as $p):
                    if ($p['kategorie_id'] == 2): $nasel_kombo = true;
                        ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail" style="flex: 3;">Detail</a>
                                <a href="smazat_oblibene.php?id=<?php echo $p['oblibene_id']; ?>"
                                   onclick="return confirm('Odebrat z výběru?')"
                                   style="flex: 1; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; border-radius: 10px; text-decoration: none; font-weight: bold;">🗑️</a>
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                <?php if (!$nasel_kombo) echo "<p style='grid-column: 1 / -1; color: #888; text-align: center; padding: 40px;'>Zatím žádná samostatná komba.</p>"; ?>
            </div>

            <div style="margin-top: 60px; padding-top: 30px; border-top: 1px solid #eee; text-align: center;">
                <a href="logout.php" class="auth-link logout" style="color: #e74c3c; font-size: 1rem; font-weight: 900;">Odhlásit se z účtu</a>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>