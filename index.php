<?php
require_once 'db_connect.php';
include_once 'templates/header.php';
?>

    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Vítejte v <span class="highlight">GuitarSelector</span></h1>
                <p>Najděte si ideální nástroj a zvuk pro vaše hraní. Využijte náš inteligentní konfigurátor nebo si prohlédněte katalog.</p>
                <div class="hero-btns">
                    <a href="dotaznik.php" class="btn-main">Spustit konfigurátor</a>
                    <a href="#video-sekce" class="btn-secondary">Přehrát video</a>
                </div>
            </div>
        </div>
    </section>

    <section id="video-sekce" class="video-section">
        <div class="container">
            <div class="video-wrapper">
                <h3>Představení projektu</h3>
                <div class="video-container">
                    <iframe
                            src="https://www.youtube.com/embed/u6_AP7CchbI"
                            title="Guitar Selection Guide"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="features-grid">

                <a href="produkty.php?kat=1" class="feature-card">
                    <div class="feature-icon">🎸</div>
                    <h3>Nabídka kytar</h3>
                    <p>Prohlédněte si elektrické a akustické kytary od světových výrobců.</p>
                    <span class="feature-link">Prozkoumat →</span>
                </a>

                <a href="produkty.php?kat=2" class="feature-card">
                    <div class="feature-icon">🔊</div>
                    <h3>Nabídka beden</h3>
                    <p>Kvalitní komba a aparáty pro váš dokonalý zvuk.</p>
                    <span class="feature-link">Prozkoumat →</span>
                </a>

                <a href="dotaznik.php" class="feature-card highlight-card">
                    <div class="feature-icon">✨</div>
                    <h3>Dotazník pro výběr</h3>
                    <p>Průvodce, který vám na základě preferencí doporučí výbavu na míru.</p>
                    <span class="feature-link">Chci poradit →</span>
                </a>

            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>