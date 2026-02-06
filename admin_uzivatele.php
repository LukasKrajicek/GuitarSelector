<?php
session_start();
require_once 'db_connect.php';

// OCHRANA: Přístup má jen tvůj Gmail
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php?error=neprilstupno");
    exit;
}

// Získání zpráv z URL (pro potvrzení smazání)
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

// OPRAVENÝ DOTAZ: Vynechali jsme datum_registrace
$sql = "SELECT id, jmeno, email FROM uzivatele ORDER BY id DESC";
$stmt = $pdo->query($sql);
$uzivatele = $stmt->fetchAll();

include_once 'templates/header.php';
?>

    <div class="detail-sekce">
        <div class="detail-wrapper" style="max-width: 900px;">
            <h1>Administrace systému</h1>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                <strong>Sekce:</strong>
                <a href="admin.php" style="color: #444; text-decoration: none;">+ Přidat produkt</a>
                <a href="admin_produkty.php" style="color: #444; text-decoration: none;">🎸 Správa produktů</a>
                <a href="admin_uzivatele.php" style="color: #f1c40f; font-weight: bold;">👥 Registrovaní uživatelé</a>
            </div>

            <?php if ($msg == 'smazano'): ?>
                <p style="background: #def0d8; color: #3c763d; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-weight: bold;">
                    Uživatel byl úspěšně odstraněn.
                </p>
            <?php endif; ?>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px; background: white;">
                <thead>
                <tr style="background: #222b31; color: white; text-align: left;">
                    <th style="padding: 12px; border: 1px solid #ddd;">ID</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">Jméno</th>
                    <th style="padding: 12px; border: 1px solid #ddd;">E-mail</th>
                    <th style="padding: 12px; border: 1px solid #ddd; text-align: center;">Akce</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($uzivatele as $u): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $u['id']; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;"><?php echo htmlspecialchars($u['jmeno']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <?php if ($u['email'] !== 'lukass.krajicek@gmail.com'): ?>
                                <a href="smazat_uzivatele.php?id=<?php echo $u['id']; ?>"
                                   onclick="return confirm('Opravdu chcete smazat tohoto uživatele?')"
                                   style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem;">
                                    Smazat
                                </a>
                            <?php else: ?>
                                <span style="color: #aaa; font-size: 0.8rem; font-style: italic;">Nelze Smazat</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (count($uzivatele) == 0): ?>
                <p style="text-align: center; padding: 20px;">Zatím žádní registrovaní uživatelé.</p>
            <?php endif; ?>

            <br>
            <a href="index.php" style="color: #666; text-decoration:none;">← Zpět na hlavní stránku</a>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>