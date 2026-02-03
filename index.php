<?php
require_once 'db_connect.php';
include_once 'templates/header.php';
?>

    <section class="welcome-section">
        <div class="hero-text">
            <h2>Vítejte v GuitarSelector</h2>
            <p>Najděte si ideální nástroj a zvuk pro vaše hraní.</p>
        </div>

        <div class="video-container" style="max-width: 800px; margin: 0 auto 50px auto; padding: 0 20px;">
            <h3 style="margin-bottom: 20px;">Úvodní video pro maturitní projekt</h3>
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <iframe
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border:0;"
                        src="https://www.youtube.com/embed/SSbBvKaM6sk"
                        title="Guitar Selection Guide"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                </iframe>
            </div>
        </div>

        <div class="main-boxes">
            <a href="produkty.php?kat=1" class="box">
                <div class="box-content">
                    <h3>Nabídka kytar</h3>
                    <p>Prohlédněte si elektrické a akustické kytary.</p>
                </div>
            </a>

            <a href="produkty.php?kat=2" class="box">
                <div class="box-content">
                    <h3>Nabídka beden</h3>
                    <p>Kvalitní komba pro vaši kytaru</p>
                </div>
            </a>

            <a href="dotaznik.php" class="box dotaznik-box">
                <div class="box-content">
                    <h3>Dotazník pro výběr</h3>
                    <p>Průvodce, který vám doporučí kytaru na míru.</p>
                </div>
            </a>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>