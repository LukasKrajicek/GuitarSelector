<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// Získání ID a ošetření
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // OPRAVENÝ SQL DOTAZ: Přidán JOIN na tabulku styly
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
    echo "<div class='detail-sekce'><div class='detail-wrapper'><h2>Produkt nebyl nalezen.</h2><br><a href='index.php' class='btn-vlozit' style='text-decoration:none; text-align:center;'>Zpět na hlavní stránku</a></div></div>";
    include_once 'templates/footer.php';
    exit;
}
?>

    <div class="detail-sekce">
        <a href="javascript:history.back()" class="btn-zpet">← Zpět na výběr</a>

        <div class="detail-wrapper">
            <span class="kategorie-tag"><?php echo htmlspecialchars($produkt['kategorie_nazev']); ?></span>

            <div class="detail-foto">
                <img src="img/<?php echo htmlspecialchars($produkt['obrazek']); ?>" alt="<?php echo htmlspecialchars($produkt['model']); ?>" style="max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 20px;">
            </div>

            <h1><?php echo htmlspecialchars($produkt['vyrobce_nazev'] . " " . $produkt['model']); ?></h1>

            <table class="detail-tabulka">
                <tr>
                    <td>Cena:</td>
                    <td class="cena-velka"><?php echo number_format($produkt['cena'], 0, ',', ' '); ?> Kč</td>
                </tr>
                <tr>
                    <td>Výrobce:</td>
                    <td><?php echo htmlspecialchars($produkt['vyrobce_nazev']); ?> (<?php echo htmlspecialchars($produkt['zeme_nazev']); ?>)</td>
                </tr>

                <?php if ($produkt['kategorie_id'] == 2): ?>
                    <?php if (!empty($produkt['vykon_w'])): ?>
                        <tr>
                            <td>Výkon:</td>
                            <td><?php echo htmlspecialchars($produkt['vykon_w']); ?> W</td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($produkt['technologie'])): ?>
                        <tr>
                            <td>Technologie:</td>
                            <td><?php echo htmlspecialchars($produkt['technologie']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <tr>
                    <td>Vhodné pro:</td>
                    <td><?php echo htmlspecialchars($produkt['uroven_nazev']); ?></td>
                </tr>
                <tr>
                    <td>Styl:</td>
                    <td><?php echo htmlspecialchars($produkt['styl_nazev']); ?></td>
                </tr>
            </table>

            <div style="max-width: 500px; margin: 20px auto;">
                <?php if (isset($_GET['msg'])): ?>
                    <?php if ($_GET['msg'] == 'ulozeno'): ?>
                        <div style="background: #27ae60; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; animation: fadeIn 0.5s;">
                            ⭐ Produkt byl uložen do tvého výběru!
                        </div>
                    <?php elseif ($_GET['msg'] == 'uz_existuje'): ?>
                        <div style="background: #e74c3c; color: white; padding: 12px; border-radius: 8px; text-align: center; font-weight: bold; animation: shake 0.5s;">
                            ❌ Tento produkt už ve svém výběru máš.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <?php if (isset($_SESSION['uzivatel_id'])): ?>
                    <a href="ulozit_oblibene.php?id=<?php echo $produkt['id']; ?>" class="btn-vlozit" style="text-decoration: none; padding: 15px 40px; font-size: 1.1rem; border-radius: 30px; transition: transform 0.2s;">
                        ⭐ Uložit do mého výběru
                    </a>
                <?php else: ?>
                    <p style="background: #f9f9f9; padding: 15px; border-radius: 10px; border: 1px dashed #ccc;">
                        Pro ukládání do výběru se musíte <a href="login.php" style="color: #f1c40f; font-weight: bold;">přihlásit</a>.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>