<?php
require_once 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = trim($_POST['jmeno']);
    $email = trim($_POST['email']);
    $heslo = $_POST['heslo'];
    $heslo2 = $_POST['heslo2'];

    // Základní kontrola
    if ($heslo !== $heslo2) {
        $error = "Hesla se neshodují!";
    } else {
        // Kontrola, zda email už neexistuje
        $check = $pdo->prepare("SELECT id FROM uzivatele WHERE email = :email");
        $check->execute(['email' => $email]);

        if ($check->fetch()) {
            $error = "Tento email už je zaregistrován!";
        } else {
            // Hashování hesla pro bezpečnost
            $hash = password_hash($heslo, PASSWORD_DEFAULT);

            $sql = "INSERT INTO uzivatele (jmeno, email, heslo) VALUES (:jmeno, :email, :heslo)";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute(['jmeno' => $jmeno, 'email' => $email, 'heslo' => $hash])) {
                header("Location: login.php?success=registrovan");
                exit;
            } else {
                $error = "Něco se nepovedlo, zkus to znovu.";
            }
        }
    }
}
include_once 'templates/header.php';
?>

    <section class="registrace-sekce">
        <div class="container">
            <div class="quiz-card" style="max-width: 500px;">
                <span class="badge" style="background: var(--dark-blue); color: var(--main-yellow);">Nová registrace</span>

                <h1 style="margin: 20px 0; color: var(--dark-blue); text-align: center;">Vytvořit účet</h1>

                <?php if ($error): ?>
                    <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #fecaca;">
                        ⚠️ <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Celé jméno</label>
                        <input type="text" name="jmeno" class="form-control" placeholder="Např. Jan Novák" required>
                    </div>

                    <div class="form-group">
                        <label>E-mailová adresa</label>
                        <input type="email" name="email" class="form-control" placeholder="vas@email.cz" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Heslo</label>
                            <input type="password" name="heslo" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="form-group">
                            <label>Kontrola hesla</label>
                            <input type="password" name="heslo2" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="width: 100%; margin-top: 10px;">
                        ZAREGISTROVAT SE
                    </button>
                </form>

                <div style="margin-top: 25px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                    <p style="color: var(--text-muted);">Už máš účet?</p>
                    <a href="login.php" style="color: var(--main-yellow); font-weight: 900; text-decoration: none;">Přihlásit se zde →</a>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'templates/footer.php'; ?>