<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $heslo = $_POST['heslo'];

    $sql = "SELECT * FROM uzivatele WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $uzivatel = $stmt->fetch();

    if ($uzivatel && password_verify($heslo, $uzivatel['heslo'])) {
        $_SESSION['uzivatel_email'] = $uzivatel['email'];
        $_SESSION['uzivatel_id'] = $uzivatel['id'];
        $_SESSION['uzivatel_jmeno'] = $uzivatel['jmeno'];

        header("Location: index.php?login=success");
        exit;
    } else {
        $error = "Špatný email nebo heslo!";
    }
}
include_once 'templates/header.php';
?>

    <section class="login-sekce">
        <div class="container">
            <div class="quiz-card" style="max-width: 450px;"> <span class="badge" style="background: var(--dark-blue); color: var(--main-yellow);">Přihlášení</span>

                <h1 style="margin: 20px 0; color: var(--dark-blue); text-align: center;">Vítejte zpět</h1>

                <?php if ($error): ?>
                    <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #fecaca;">
                        ⚠️ <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>E-mailová adresa</label>
                        <input type="email" name="email" class="form-control" placeholder="jmeno@seznam.cz" required>
                    </div>

                    <div class="form-group">
                        <label>Heslo</label>
                        <input type="password" name="heslo" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-submit" style="width: 100%; margin-top: 10px;">
                        PŘIHLÁSIT SE
                    </button>
                </form>

                <div style="margin-top: 25px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                    <p style="color: var(--text-muted);">Nemáš ještě účet?</p>
                    <a href="registrace.php" style="color: var(--main-yellow); font-weight: 900; text-decoration: none;">Vytvořit účet →</a>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>