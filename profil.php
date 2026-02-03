<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// Pokud není přihlášen, nepustíme ho sem
if (!isset($_SESSION['uzivatel_id'])) {
    header("Location: login.php?error=nemate-pristup");
    exit;
}

$uzivatel_id = $_SESSION['uzivatel_id'];

// SQL dotaz pro získání uložených produktů konkrétního uživatele
$sql = "SELECT p.*, v.nazev AS vyrobce_nazev, uv.id AS vyber_id 
        FROM ulozeny_vyber uv
        JOIN produkty p ON uv.produkt_id = p.id
        JOIN vyrobci v ON p.vyrobce_id = v.id
        WHERE uv.uzivatel_id = :uid";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $uzivatel_id]);
$ulozeno = $stmt->fetchAll();
?>

    <div class="produkty-sekce">
        <div class="container">
            <h1>Můj uložený výběr</h1>
            <p style="margin-bottom: 30px;">Zde jsou nástroje, které tě zaujaly během průvodce nebo v katalogu.</p>

            <div class="produkty-grid">
                <?php if (count($ulozeno) > 0): ?>
                    <?php foreach ($ulozeno as $p): ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto" style="max-width:100%; height:150px; object-fit:contain;">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>

                            <div style="display: flex; gap: 10px; margin-top: auto;">
                                <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail" style="flex: 1;">Detail</a>
                                <a href="smazat_vyber.php?id=<?php echo $p['vyber_id']; ?>"
                                   onclick="return confirm('Opravdu chcete tento produkt odebrat z výběru?')"
                                   style="background: #e74c3c; color: white; text-align: center; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                                    Smazat
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="detail-wrapper" style="text-align: center; grid-column: 1 / -1;">
                        <p>Zatím nemáš nic uloženo. Zkus náš <a href="dotaznik.php">průvodce výběrem</a>!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>