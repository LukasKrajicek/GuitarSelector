<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// Získání ID a ošetření
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "SELECT p.*, 
                   v.nazev AS vyrobce_nazev, 
                   u.nazev AS uroven_nazev, 
                   z.nazev AS zeme_nazev,
                   k.nazev AS kategorie_nazev,
                   s.nazev AS styl_nazev
            FROM produkty p
            JOIN vyrobci v ON p.vyrobce_id = v.id
            JOIN urovne u ON p.uroven_id = u.id
            JOIN zeme z ON v.zeme_id = z.id
            JOIN kategorie k ON p.kategorie_id = k.id
            JOIN styly s ON p.styl_id = s.id
            WHERE p.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $produkt = $stmt->fetch();
} else {
    $produkt = false;
}

if (!$produkt) {
    echo "<section class='container'><div class='quiz-card' style='text-align:center;'><h2>Produkt nebyl nalezen.</h2><br><a href='index.php' class='btn-main'>Zpět na hlavní stránku</a></div></section>";
    include_once 'templates/footer.php';
    exit;
}
?>

    <section class="container" style="padding-top: 40px;">
        <a href="javascript:history.back()" class="btn-reset" style="margin-bottom: 20px; display: inline-block;">← Zpět na výběr</a>

        <div class="quiz-card" style="max-width: 900px;"> <span class="badge" style="background: var(--dark-blue); color: var(--main-yellow); font-size: 0.9rem;">
            <?php echo htmlspecialchars($produkt['kategorie_nazev']); ?>
        </span>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-top: 20px;">

                <div class="detail-foto">
                    <div class="img-box" style="height: auto; min-height: 300px; background: #f9f9f9; border-radius: 15px; padding: 20px;">
                        <img src="img/<?php echo htmlspecialchars($produkt['obrazek']); ?>" alt="foto" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                    </div>
                </div>

                <div class="detail-info">
                    <h1 style="color: var(--dark-blue); margin-bottom: 10px; font-weight: 900;">
                        <?php echo htmlspecialchars($produkt['vyrobce_nazev'] . " " . $produkt['model']); ?>
                    </h1>

                    <div class="cena" style="font-size: 2.2rem; margin-bottom: 25px;">
                        <?php echo number_format($produkt['cena'], 0, ',', ' '); ?> Kč
                    </div>

                    <div class="vlastnosti" style="border-top: 1px solid #eee; padding-top: 20px;">
                        <p><strong>Výrobce:</strong> <?php echo htmlspecialchars($produkt['vyrobce_nazev']); ?> (<?php echo htmlspecialchars($produkt['zeme_nazev']); ?>)</p>
                        <p><strong>Úroveň:</strong> <?php echo htmlspecialchars($produkt['uroven_nazev']); ?></p>
                        <p><strong>Styl:</strong> <?php echo htmlspecialchars($produkt['styl_nazev']); ?></p>

                        <?php if ($produkt['kategorie_id'] == 2): ?>
                            <p><strong>Technologie:</strong> <?php echo htmlspecialchars($produkt['technologie']); ?></p>
                            <p><strong>Výkon:</strong> <?php echo htmlspecialchars($produkt['vykon_w']); ?> W</p>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 30px;">
                        <?php if (isset($_SESSION['uzivatel_id'])): ?>
                            <a href="ulozit_oblibene.php?id=<?php echo $produkt['id']; ?>" class="btn-vlozit">
                                ⭐ Uložit do mého výběru
                            </a>
                        <?php else: ?>
                            <div style="background: #f4f7f9; padding: 20px; border-radius: 12px; text-align: center; border: 2px dashed #ddd;">
                                <p>Pro ukládání se musíš <a href="login.php" style="color: var(--main-yellow); font-weight: bold;">přihlásit</a>.</p>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['msg'])): ?>
                            <div style="margin-top: 15px;">
                                <?php if ($_GET['msg'] == 'ulozeno'): ?>
                                    <div style="background: #27ae60; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold;">
                                        ⭐ Uloženo do profilu!
                                    </div>
                                <?php elseif ($_GET['msg'] == 'uz_existuje'): ?>
                                    <div style="background: #e67e22; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold;">
                                        Tento kousek už ve výběru máš.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>