<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php");
    exit;
}

include_once 'templates/header.php';

// Načtení uživatelů
$uzivatele = $pdo->query("SELECT * FROM uzivatele ORDER BY id DESC")->fetchAll();
?>

    <section class="admin-sekce">
        <div class="container">
            <div class="quiz-card" style="margin-bottom: 30px; padding: 20px;">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                    <h2 style="margin:0; font-size: 1.2rem; border-right: 2px solid #eee; padding-right: 20px;">🛠️ Administrace</h2>
                    <a href="admin.php" style="text-decoration:none; color: var(--text-muted);">+ Přidat produkt</a>
                    <a href="admin_produkty.php" style="text-decoration:none; color: var(--text-muted);">🎸 Správa produktů</a>
                    <a href="admin_uzivatele.php" class="feature-link" style="color: var(--main-yellow);">👥 Registrovaní uživatelé</a>
                </div>
            </div>

            <div class="quiz-card">
                <h1 style="margin-bottom: 20px;">Správa uživatelů</h1>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jméno</th>
                            <th>E-mail</th>
                            <th>Role</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($uzivatele as $u): ?>
                            <tr>
                                <td>#<?php echo $u['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($u['jmeno']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <?php echo ($u['email'] === 'lukass.krajicek@gmail.com') ? '<span class="badge">Admin</span>' : 'Uživatel'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>