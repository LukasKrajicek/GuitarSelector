<?php
session_start();
require_once 'db_connect.php';

// OCHRANA: Přístup má jen tvůj Gmail
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=nemate-pristup");
    exit;
}

include_once 'templates/header.php';
?>

    <div class="detail-sekce">
        <div class="detail-wrapper" style="max-width: 1000px;">
            <h1>Správa produktů</h1>
            <p>Zde můžete kontrolovat a mazat kytary z nabídky.</p>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                <strong>Sekce:</strong>
                <a href="admin.php" style="color: #444; text-decoration: none;">+ Přidat produkt</a>
                <a href="admin_produkty.php" style="color: #f1c40f; font-weight: bold;">🎸 Správa produktů</a>
                <a href="admin_uzivatele.php" style="color: #444; text-decoration: none;">👥 Registrovaní uživatelé</a>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'smazano'): ?>
                <p style="color: green; font-weight: bold; margin-bottom: 15px;">Produkt byl úspěšně odstraněn.</p>
            <?php endif; ?>

            <div style="overflow-x: auto;">
                <table style="width:100%; border-collapse: collapse; background: white; border: 1px solid #ddd;">
                    <thead>
                    <tr style="background: #222b31; color: white; text-align: left;">
                        <th style="padding: 12px; border: 1px solid #ddd;">Model</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Cena</th>
                        <th style="padding: 12px; border: 1px solid #ddd; text-align: center;">Akce</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT p.id, p.model, p.cena, v.nazev AS vyrobce 
                            FROM produkty p 
                            LEFT JOIN vyrobci v ON p.vyrobce_id = v.id 
                            ORDER BY p.id DESC";
                    $stmt = $pdo->query($sql);
                    while ($p = $stmt->fetch()):
                        ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <strong><?php echo htmlspecialchars($p['vyrobce']); ?></strong> <?php echo htmlspecialchars($p['model']); ?>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                                <a href="smazat_produkt.php?id=<?php echo $p['id']; ?>"
                                   onclick="return confirm('Opravdu smazat tento produkt?')"
                                   style="color: white; background: #e74c3c; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem;">
                                    Smazat
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <br>
            <a href="index.php" style="color: #666; text-decoration:none;">← Zpět na hlavní stránku</a>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>