<?php
require_once 'db_connect.php';
include_once 'templates/header.php';
?>

    <section class="welcome-section">
        <div class="hero-text">
            <h2>Vítejte v GuitarSelector</h2>
            <p>Najděte si ideální nástroj a zvuk pro vaši hru.</p>
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