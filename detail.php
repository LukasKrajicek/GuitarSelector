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
                   k.nazev AS kategorie_nazev
            FROM produkty p
            JOIN vyrobci v ON p.vyrobce_id = v.id
            JOIN urovne u ON p.uroven_id = u.id
            JOIN zeme z ON v.zeme_id = z.id
            JOIN kategorie k ON p.kategorie_id = k.id
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
                <tr>
                    <td>Vhodné pro:</td>
                    <td><?php echo htmlspecialchars($produkt['uroven_nazev']); ?></td>
                </tr>
                <tr>
                    <td>Styl:</td>
                    <td><?php echo htmlspecialchars($produkt['styl']); ?></td>
                </tr>

                <?php if ($produkt['kategorie_id'] == 2): ?>
                    <tr>
                        <td>Výkon:</td>
                        <td><?php echo htmlspecialchars($produkt['vykon_w'] ?? 'Neznámý'); ?> W</td>
                    </tr>
                    <tr>
                        <td>Technologie:</td>
                        <td><?php echo htmlspecialchars($produkt['technologie'] ?? 'Neznámá'); ?></td>
                    </tr>
                <?php endif; ?>
            </table>

            <button class="btn-vlozit">Uložit do mého výběru</button>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>