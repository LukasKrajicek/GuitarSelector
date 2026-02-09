<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// Zjistíme kategorii z URL (1 = Kytara, 2 = Kombo)
$kategorie_id = isset($_GET['kat']) ? (int)$_GET['kat'] : 1;

// SQL dotaz: Přidali jsme JOIN na tabulku STYLY, abychom dostali název místo ID
$sql = "SELECT p.*, 
               v.nazev AS vyrobce_nazev, 
               u.nazev AS uroven_nazev,
               z.nazev AS zeme_puvodu,
               s.nazev AS styl_nazev
        FROM produkty p 
        JOIN vyrobci v ON p.vyrobce_id = v.id 
        JOIN urovne u ON p.uroven_id = u.id
        JOIN zeme z ON v.zeme_id = z.id
        JOIN styly s ON p.styl_id = s.id
        WHERE p.kategorie_id = :kat_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['kat_id' => $kategorie_id]);
$produkty = $stmt->fetchAll();

$nadpis = ($kategorie_id === 2) ? "Nabídka beden a komb" : "Nabídka kytar";
?>

    <section class="produkty-sekce">
        <div class="container">
            <h2><?php echo $nadpis; ?></h2>

            <div class="produkty-grid">
                <?php if (count($produkty) > 0): ?>
                    <?php foreach ($produkty as $p): ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small" style="text-align:center; margin-bottom:15px;">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto" style="max-height:150px; object-fit:contain;">
                            </div>

                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p><strong>Úroveň:</strong> <?php echo htmlspecialchars($p['uroven_nazev']); ?></p>

                            <p><strong>Styl:</strong> <?php echo htmlspecialchars($p['styl_nazev']); ?></p>

                            <p><strong>Původ:</strong> <?php echo htmlspecialchars($p['zeme_puvodu']); ?></p>

                            <?php if ($kategorie_id === 2): ?>
                                <p><strong>Typ:</strong> <?php echo htmlspecialchars($p['technologie']); ?> (<?php echo (int)$p['vykon_w']; ?>W)</p>
                            <?php endif; ?>

                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">Zobrazit detail</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                        <p>V této kategorii zatím nejsou žádné produkty.</p>
                        <a href="admin.php" style="color: #f1c40f;">Přidat první produkt</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>