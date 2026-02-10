<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['uzivatel_id']) || $_SESSION['uzivatel_email'] !== 'lukass.krajicek@gmail.com') {
    header("Location: index.php");
    exit;
}

include_once 'templates/header.php';

// Načtení všech produktů
$produkty = $pdo->query("SELECT p.*, v.nazev as vyrobce, k.nazev as kategorie FROM produkty p 
                         JOIN vyrobci v ON p.vyrobce_id = v.id 
                         JOIN kategorie k ON p.kategorie_id = k.id 
                         ORDER BY p.id DESC")->fetchAll();
?>

    <section class="admin-sekce">
        <div class="container">
            <div class="quiz-card" style="margin-bottom: 30px; padding: 20px;">
                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                    <h2 style="margin:0; font-size: 1.2rem; border-right: 2px solid #eee; padding-right: 20px;">🛠️ Administrace</h2>
                    <a href="admin.php" style="text-decoration:none; color: var(--text-muted);">+ Přidat produkt</a>
                    <a href="admin_produkty.php" class="feature-link" style="color: var(--main-yellow);">🎸 Správa produktů</a>
                    <a href="admin_uzivatele.php" style="text-decoration:none; color: var(--text-muted);">👥 Registrovaní uživatelé</a>
                </div>
            </div>

            <div class="quiz-card">
                <h1 style="margin-bottom: 20px;">Seznam všech produktů</h1>

                <div style="overflow-x: auto;"> <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Výrobce a Model</th>
                            <th>Kategorie</th>
                            <th>Cena</th>
                            <th>Akce</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($produkty as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><img src="img/<?php echo $p['obrazek']; ?>" style="width: 50px; height: 50px; object-fit: contain; background: #f9f9f9; border-radius: 5px;"></td>
                                <td><strong><?php echo htmlspecialchars($p['vyrobce'] . " " . $p['model']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['kategorie']); ?></td>
                                <td><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</td>
                                <td>
                                    <a href="smazat_produkt.php?id=<?php echo $p['id']; ?>"
                                       style="color: #e74c3c; text-decoration: none; font-weight: bold;"
                                       onclick="return confirm('Opravdu smazat?')">Smazat</a>
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