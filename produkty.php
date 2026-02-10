<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

// 1. NAČTENÍ FILTRŮ Z URL
$kategorie_id = isset($_GET['kat']) ? (int)$_GET['kat'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$styl_id = isset($_GET['styl_filter']) ? (int)$_GET['styl_filter'] : 0;
$max_cena = isset($_GET['max_cena']) ? (int)$_GET['max_cena'] : 150000;

// 2. NAČTENÍ STYLŮ PRO DROPDOWN FILTR
$styly_pro_filtr = $pdo->query("SELECT * FROM styly ORDER BY nazev")->fetchAll();

// 3. SESTAVENÍ SQL DOTAZU S FILTRY
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

if (!empty($search)) $sql .= " AND (p.model LIKE :search OR v.nazev LIKE :search)";
if ($styl_id > 0) $sql .= " AND p.styl_id = :styl_id";
$sql .= " AND p.cena <= :max_cena ORDER BY p.cena ASC";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':kat_id', $kategorie_id, PDO::PARAM_INT);
$stmt->bindValue(':max_cena', $max_cena, PDO::PARAM_INT);
if (!empty($search)) $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
if ($styl_id > 0) $stmt->bindValue(':styl_id', $styl_id, PDO::PARAM_INT);

$stmt->execute();
$produkty = $stmt->fetchAll();

$nadpis = ($kategorie_id === 2) ? "Nabídka beden a komb" : "Nabídka kytar";
?>

    <section class="produkty-sekce">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2><?php echo $nadpis; ?></h2>
                <button type="button" class="btn-open-filter" onclick="toggleFilter()">
                    🔍 Filtry a hledání
                </button>
            </div>

            <div class="filter-sidebar" id="filterSidebar">
                <div class="filter-header">
                    <h3>Filtrování</h3>
                    <button type="button" class="btn-close-filter" onclick="toggleFilter()">&times;</button>
                </div>

                <form method="GET" class="filter-form">
                    <input type="hidden" name="kat" value="<?php echo $kategorie_id; ?>">

                    <div class="filter-group">
                        <label>Hledat model/značku</label>
                        <input type="text" name="search" class="form-control" placeholder="Např. Stratocaster..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div class="filter-group">
                        <label>Hudební styl</label>
                        <select name="styl_filter" class="form-control">
                            <option value="0">Všechny styly</option>
                            <?php foreach ($styly_pro_filtr as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo ($styl_id == $s['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['nazev']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Max. cena: <span id="priceValue" style="color:var(--main-yellow);"><?php echo number_format($max_cena, 0, ',', ' '); ?></span> Kč</label>
                        <input type="range" name="max_cena" min="1000" max="150000" step="1000"
                               value="<?php echo $max_cena; ?>" class="range-slider" id="priceRange">
                    </div>

                    <button type="submit" class="btn-filter" style="width: 100%; margin-top: 10px;">Použít filtry</button>
                    <a href="?kat=<?php echo $kategorie_id; ?>" class="btn-reset" style="display: block; text-align: center; margin-top: 15px;">Zrušit vše</a>
                </form>
            </div>

            <div class="filter-overlay" id="filterOverlay" onclick="toggleFilter()"></div>

            <div class="produkty-grid">
                <?php if (count($produkty) > 0): ?>
                    <?php foreach ($produkty as $p): ?>
                        <div class="produkt-karta">
                            <div class="produkt-foto-small">
                                <img src="img/<?php echo htmlspecialchars($p['obrazek']); ?>" alt="foto">
                            </div>
                            <h3><?php echo htmlspecialchars($p['vyrobce_nazev'] . " " . $p['model']); ?></h3>
                            <p><strong>Úroveň:</strong> <?php echo htmlspecialchars($p['uroven_nazev']); ?></p>
                            <p><strong>Styl:</strong> <?php echo htmlspecialchars($p['styl_nazev']); ?></p>
                            <?php if ($kategorie_id === 2): ?>
                                <p><strong>Typ:</strong> <?php echo htmlspecialchars($p['technologie']); ?> (<?php echo (int)$p['vykon_w']; ?>W)</p>
                            <?php endif; ?>
                            <p class="cena"><?php echo number_format($p['cena'], 0, ',', ' '); ?> Kč</p>
                            <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-detail">Zobrazit detail</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                        <p>Pro tento výběr nebyly nalezeny žádné produkty.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        function toggleFilter() {
            document.getElementById("filterSidebar").classList.toggle("active");
            document.getElementById("filterOverlay").classList.toggle("active");
        }

        const slider = document.getElementById("priceRange");
        const output = document.getElementById("priceValue");
        slider.oninput = function() {
            output.innerHTML = parseInt(this.value).toLocaleString('cs-CZ');
        }
    </script>

<?php include_once 'templates/footer.php'; ?>