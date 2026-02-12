<?php
session_start();
require_once 'db_connect.php';
include_once 'templates/header.php';

// 1. Načtení hud. stylů z databáze
$styly_db = $pdo->query("SELECT * FROM styly ORDER BY nazev")->fetchAll();

$vysledek = null;
$hledal_set = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $typ = $_POST['typ'];
    $hledal_set = ($typ == 'set');
    $styl_id = (int)$_POST['styl_id'];
    $uroven = (int)$_POST['uroven'];
    $budget = (int)$_POST['budget'];

    // Nové parametry pro komba
    $misto = isset($_POST['misto']) ? $_POST['misto'] : 'doma';
    $tech = isset($_POST['technologie']) ? $_POST['technologie'] : '';

    $limit_kombo = ($hledal_set) ? $budget * 0.4 : $budget;
    $limit_kytara = ($hledal_set) ? $budget * 0.6 : $budget;

    try {
        // Hledání kytary
        if ($typ == 'kytara' || $hledal_set) {
            $stmt = $pdo->prepare("SELECT p.*, v.nazev as vyrobce FROM produkty p JOIN vyrobci v ON p.vyrobce_id = v.id WHERE p.kategorie_id = 1 AND p.styl_id = :styl_id AND p.uroven_id = :uroven AND p.cena <= :budget ORDER BY p.cena DESC LIMIT 1");
            $stmt->execute(['styl_id' => $styl_id, 'uroven' => $uroven, 'budget' => $limit_kytara]);
            $vysledek['kytara'] = $stmt->fetch();
        }

        // Hledání komba s rozšířenými filtry
        if ($typ == 'kombo' || $hledal_set) {
            $params = ['uroven' => $uroven, 'budget' => $limit_kombo];
            $sql_k = "SELECT p.*, v.nazev as vyrobce FROM produkty p JOIN vyrobci v ON p.vyrobce_id = v.id WHERE p.kategorie_id = 2 AND p.uroven_id = :uroven AND p.cena <= :budget ";

            // Filtr na místo (výkon)
            if ($misto == 'doma') {
                $sql_k .= " AND p.vykon_w <= 20 ";
            } else {
                $sql_k .= " AND p.vykon_w >= 30 ";
            }

            // Filtr na technologii (pokud je vybrána)
            if (!empty($tech)) {
                $sql_k .= " AND p.technologie = :tech ";
                $params['tech'] = $tech;
            }

            $stmt = $pdo->prepare($sql_k . " ORDER BY p.cena DESC LIMIT 1");
            $stmt->execute($params);
            $vysledek['kombo'] = $stmt->fetch();
        }
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
?>

    <div class="quiz-container">
        <div class="quiz-card">
            <?php if (!$vysledek): ?>
                <h1 class="quiz-title">🎸 Hudební konfigurátor</h1>
                <form method="POST" id="quizForm">
                    <div class="form-group">
                        <label>Co hledáš za vybavení?</label>
                        <select name="typ" id="typVyberu" class="form-control" onchange="toggleFields()">
                            <option value="set">Kompletní SET (Kytara + Kombo)</option>
                            <option value="kytara">Samostatná kytara</option>
                            <option value="kombo">Samostatné kombo</option>
                        </select>
                    </div>

                    <div class="form-group">
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
                            <label>Maximální budget</label>
                            <input type="number" name="budget" class="form-control" value="20000">
                        </div>
                    </div>

                    <div id="komboSekce">
                        <div class="form-group">
                            <label>Kde na to budeš řádit?</label>
                            <select name="misto" class="form-control">
                                <option value="doma">Doma v pokoji (tiché kombo do 20W)</option>
                                <option value="kapela">Ve zkušebně / Koncerty (nad 30W)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Preferovaná technologie aparátu</label>
                            <select name="technologie" class="form-control">
                                <option value="">Je mi to jedno (Doporučit nejlepší)</option>
                                <option value="Lampové">Lampové (Tradiční zvuk)</option>
                                <option value="Tranzistorové">Tranzistorové (Spolehlivost)</option>
                                <option value="Modelingové">Modelingové (Mnoho efektů)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">NAJÍT IDEÁLNÍ VÝBAVU</button>
                </form>

            <?php else: ?>
                <h1 class="quiz-title">Tvoje výbava na míru ✨</h1>
                <div class="result-grid">
                    <?php foreach ($vysledek as $klic => $p): if ($p): ?>
                        <div class="result-item">
                            <div class="badge"><?php echo strtoupper($klic); ?></div>
                            <div class="img-box"><img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>"></div>
                            <h3 style="margin: 15px 0;"><?php echo htmlspecialchars($p['vyrobce']." ".$p['model']); ?></h3>
                            <?php if($klic == 'kombo'): ?>
                                <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                                    <?php echo $p['technologie']; ?> • <?php echo $p['vykon_w']; ?>W
                                </p>
                            <?php endif; ?>
                            <div style="font-weight: 900; font-size: 1.6rem; color: var(--dark-blue);">
                                <?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč
                            </div>
                        </div>
                    <?php endif; endforeach; ?>
                </div>

                <div style="margin-top: 30px;">
                    <?php if ($hledal_set && isset($vysledek['kytara']['id']) && isset($vysledek['kombo']['id'])): ?>
                        <a href="ulozit_set.php?k_id=<?php echo $vysledek['kytara']['id']; ?>&a_id=<?php echo $vysledek['kombo']['id']; ?>" class="btn-vlozit">
                            💾 ULOŽIT KOMPLETNÍ SET DO PROFILU
                        </a>
                    <?php elseif (isset($vysledek['kytara']['id']) || isset($vysledek['kombo']['id'])): ?>
                        <?php $p_id = isset($vysledek['kytara']['id']) ? $vysledek['kytara']['id'] : $vysledek['kombo']['id']; ?>
                        <a href="ulozit_oblibene.php?id=<?php echo $p_id; ?>" class="btn-vlozit">
                            ⭐ ULOŽIT DO MÉHO VÝBĚRU
                        </a>
                    <?php endif; ?>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="dotaznik.php" style="color: #888; text-decoration: none;">← ZKUSIT DOTAZNÍK ZNOVU</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleFields() {
            var typ = document.getElementById("typVyberu").value;
            var komboSekce = document.getElementById("komboSekce");

            // Pokud je vybrána kytara, skryjeme sekci pro kombo
            if (typ === "kytara") {
                komboSekce.style.display = "none";
            } else {
                komboSekce.style.display = "block";
            }
        }

        // Spustit při načtení pro správný stav
        window.onload = toggleFields;
    </script>

<?php include_once 'templates/footer.php'; ?>