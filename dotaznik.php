<?php
session_start();
require_once 'db_connect.php';
include_once 'templates/header.php';

// 1. Načtení stylů z nové tabulky pro výběr ve formuláři
$styly_db = $pdo->query("SELECT * FROM styly ORDER BY nazev")->fetchAll();

$vysledek = null;
$hledal_set = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $typ = $_POST['typ'];
    $hledal_set = ($typ == 'set');
    $styl_id = (int)$_POST['styl_id']; // Nyní přijímáme ID, ne text
    $uroven = (int)$_POST['uroven'];
    $budget = (int)$_POST['budget'];
    $misto = $_POST['misto'];

    $limit_kombo = ($hledal_set) ? $budget * 0.4 : $budget;
    $limit_kytara = ($hledal_set) ? $budget * 0.6 : $budget;

    try {
        // Hledání kytary (styl_id)
        if ($typ == 'kytara' || $hledal_set) {
            $stmt = $pdo->prepare("SELECT p.*, v.nazev as vyrobce FROM produkty p JOIN vyrobci v ON p.vyrobce_id = v.id WHERE p.kategorie_id = 1 AND p.styl_id = :styl_id AND p.uroven_id = :uroven AND p.cena <= :budget ORDER BY p.cena DESC LIMIT 1");
            $stmt->execute(['styl_id' => $styl_id, 'uroven' => $uroven, 'budget' => $limit_kytara]);
            $vysledek['kytara'] = $stmt->fetch();
        }

        // Hledání komba
        if ($typ == 'kombo' || $hledal_set) {
            $sql_k = "SELECT p.*, v.nazev as vyrobce FROM produkty p JOIN vyrobci v ON p.vyrobce_id = v.id WHERE p.kategorie_id = 2 AND p.uroven_id = :uroven AND p.cena <= :budget ";
            $sql_k .= ($misto == 'doma') ? " AND p.vykon_w <= 20 " : " AND p.vykon_w >= 30 ";
            $stmt = $pdo->prepare($sql_k . " ORDER BY p.cena DESC LIMIT 1");
            $stmt->execute(['uroven' => $uroven, 'budget' => $limit_kombo]);
            $vysledek['kombo'] = $stmt->fetch();
        }
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>

    <style>
        :root { --main-yellow: #f1c40f; --dark-blue: #222b31; --soft-bg: #fdfcf0; }
        .quiz-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .quiz-card { background: white; border-radius: 25px; padding: 40px; border: 4px solid var(--main-yellow); box-shadow: 0 20px 50px rgba(0,0,0,0.15); }
        .quiz-title { text-align: center; color: var(--dark-blue); font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; border-bottom: 5px solid var(--main-yellow); display: inline-block; width: 100%; padding-bottom: 10px; }

        .form-group label { font-weight: bold; color: var(--dark-blue); text-transform: uppercase; font-size: 0.9rem; }
        .form-control { border: 2px solid #eee; padding: 15px; border-radius: 12px; width: 100%; margin-top: 8px; background: #fafafa; font-size: 1rem; }
        .form-control:focus { border-color: var(--main-yellow); background: white; outline: none; box-shadow: 0 0 10px rgba(241, 196, 15, 0.2); }

        .btn-submit { background: var(--main-yellow); color: var(--dark-blue); border: none; padding: 20px; border-radius: 15px; width: 100%; font-weight: 900; cursor: pointer; font-size: 1.3rem; margin-top: 20px; transition: 0.3s; box-shadow: 0 5px 0 #d4ac0d; }
        .btn-submit:hover { transform: translateY(2px); box-shadow: 0 2px 0 #d4ac0d; background: #ffda44; }

        .result-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 30px; }
        .result-item { background: white; border: 4px solid var(--main-yellow); border-radius: 20px; padding: 25px; text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }

        .img-box { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; height: 220px; display: flex; align-items: center; justify-content: center; border: 1px solid #f0f0f0; }
        .img-box img { max-height: 100%; max-width: 100%; object-fit: contain; }

        .btn-set-save { background: #27ae60; color: white; padding: 22px; border-radius: 15px; text-decoration: none; display: block; text-align: center; margin-top: 30px; font-weight: 900; font-size: 1.4rem; transition: 0.3s; cursor: pointer; border:none; width:100%; }
        .btn-set-save:hover { background: #2ecc71; transform: scale(1.02); }

        .badge { background: var(--dark-blue); color: var(--main-yellow); padding: 6px 15px; border-radius: 8px; font-size: 0.8rem; font-weight: 900; margin-bottom: 10px; display: inline-block; }
    </style>

    <div class="quiz-container">
        <div class="quiz-card">
            <?php if (!$vysledek): ?>
                <h1 class="quiz-title">🎸 Hudební konfigurátor</h1>
                <form method="POST">
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label>Co hledáš za vybavení?</label>
                        <select name="typ" class="form-control">
                            <option value="set">Kompletní SET (Kytara + Kombo) - Nejlepší volba</option>
                            <option value="kytara">Samostatná kytara</option>
                            <option value="kombo">Samostatné kombo</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label>Tvůj hudební styl</label>
                        <select name="styl_id" class="form-control" required>
                            <option value="">-- Vyber svůj styl --</option>
                            <?php foreach ($styly_db as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nazev']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Tvoje úroveň</label>
                            <select name="uroven" class="form-control">
                                <option value="1">Začátečník</option>
                                <option value="2">Pokročilý</option>
                                <option value="3">Expert / Profesionál</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Maximální budget (Kč)</label>
                            <input type="number" name="budget" class="form-control" value="20000">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label>Kde na to budeš řádit?</label>
                        <select name="misto" class="form-control">
                            <option value="doma">Doma v pokoji (tiché kombo)</option>
                            <option value="kapela">Ve zkušebně / Koncerty (pořádný výkon)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">NAJÍT IDEÁLNÍ SESTAVU</button>
                </form>

            <?php else: ?>
                <h1 class="quiz-title">Tvoje výbava na míru ✨</h1>
                <div class="result-grid">
                    <?php foreach ($vysledek as $klic => $p): if ($p): ?>
                        <div class="result-item">
                            <div class="badge"><?php echo strtoupper($klic); ?></div>
                            <div class="img-box"><img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>"></div>
                            <h3 style="margin: 15px 0; color: var(--dark-blue);"><?php echo htmlspecialchars($p['vyrobce']." ".$p['model']); ?></h3>
                            <div style="font-weight: 900; font-size: 1.6rem; color: var(--dark-blue); margin-bottom: 15px;">
                                <?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                </div>

                <?php if ($hledal_set && isset($vysledek['kytara']['id']) && isset($vysledek['kombo']['id'])): ?>
                    <a href="ulozit_set.php?k_id=<?php echo $vysledek['kytara']['id']; ?>&a_id=<?php echo $vysledek['kombo']['id']; ?>" class="btn-set-save">
                        💾 ULOŽIT KOMPLETNÍ SET DO PROFILU
                    </a>
                <?php elseif (isset($vysledek['kytara']['id']) || isset($vysledek['kombo']['id'])): ?>
                    <?php
                    // Získáme ID produktu, který byl nalezen
                    $p_id = isset($vysledek['kytara']['id']) ? $vysledek['kytara']['id'] : $vysledek['kombo']['id'];
                    ?>
                    <a href="ulozit_oblibene.php?id=<?php echo $p_id; ?>" class="btn-set-save" style="background: var(--dark-blue); color: var(--main-yellow);">
                        ⭐ ULOŽIT DO MÉHO VÝBĚRU
                    </a>
                <?php else: ?>
                    <p style="text-align: center; padding: 20px; font-weight: bold;">Pro zadaná kritéria jsme nic nenašli. Zkus upravit styl nebo budget.</p>
                <?php endif; ?>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="dotaznik.php" style="color: #888; text-decoration: none; font-weight: bold;">← ZKUSIT DOTAZNÍK ZNOVU</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>