<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// Získání dat z URL (metoda GET)
$kat = isset($_GET['kat']) ? (int)$_GET['kat'] : 1;
$uroven = isset($_GET['uroven']) ? (int)$_GET['uroven'] : 1;
$cena_max = isset($_GET['cena_max']) ? (int)$_GET['cena_max'] : 100000;

// SQL dotaz, který filtruje podle odpovědí
$sql = "SELECT p.*, v.nazev AS vyrobce_nazev, u.nazev AS uroven_nazev 
        FROM produkty p 
        JOIN vyrobci v ON p.vyrobce_id = v.id 
        JOIN urovne u ON p.uroven_id = u.id
        WHERE p.kategorie_id = :kat 
        AND p.uroven_id = :uroven 
        AND p.cena <= :cena_max
        ORDER BY p.cena DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'kat' => $kat,
    'uroven' => $uroven,
    'cena_max' => $cena_max
]);
$vysledky = $stmt->fetchAll();
?>

<div class="produkty-sekce">
    <div class="container">
        <h1>Naše doporučení pro tebe</h1>
        <p style="margin-bottom: 30px;">Na základě tvých odpovědí jsme vybrali tyto nástroje:</p>

        <div class="produkty-grid">
            <?php if (count($vysledky) > 0): ?>
                <?php foreach ($vysledky as $p): ?>
                    <div class="produkt-karta">
                        <div class="produkt-foto-small">
                            <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto" style="max-width:100%; height:150px; object-fit:contain;">
                        </div>
                        <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                        <p>Úroveň: <?php echo htmlspecialchars($p['uroven_nazev']); ?></p>
                        <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                        <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">Chci tento!</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="detail-wrapper" style="text-align: center; grid-column: 1 / -1;">
                    <h2>Bohužel jsme nic nenašli 🎸</h2>
                    <p>Zkus zvýšit rozpočet nebo změnit úroveň pokročilosti.</p>
                    <br>
                    <a href="dotaznik.php" class="btn-vlozit" style="text-decoration:none;">Zkusit dotazník znovu</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
