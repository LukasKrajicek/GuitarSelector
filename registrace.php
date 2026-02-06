<?php
require_once 'db_connect.php';
include_once 'templates/header.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = trim($_POST['jmeno']);
    $prijmeni = trim($_POST['prijmeni']);
    $email = trim($_POST['email']);
    $heslo = $_POST['heslo'];

    // 1. VALIDACE: Jsou pole vyplněná?
    if (empty($jmeno) || empty($prijmeni) || empty($email) || empty($heslo)) {
        $error = "Všechna pole jsou povinná!";
    }
    // 2. VALIDACE: Je heslo dost dlouhé? (Maturitní pojistka)
    elseif (strlen($heslo) < 6) {
        $error = "Heslo musí mít alespoň 6 znaků!";
    }
    else {
        try {
            // 3. KONTROLA: Existuje už tento email?
            $checkEmail = $pdo->prepare("SELECT id FROM uzivatele WHERE email = ?");
            $checkEmail->execute([$email]);

            if ($checkEmail->fetch()) {
                $error = "Tento email už je u nás registrovaný. Zkuste se přihlásit.";
            } else {
                // 4. Vše je OK -> Hashování a zápis
                $hashed_heslo = password_hash($heslo, PASSWORD_DEFAULT);
                $sql = "INSERT INTO uzivatele (jmeno, prijmeni, email, heslo) VALUES (:jmeno, :prijmeni, :email, :heslo)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                        'jmeno' => $jmeno,
                        'prijmeni' => $prijmeni,
                        'email' => $email,
                        'heslo' => $hashed_heslo
                ]);
                $success = "Registrace proběhla úspěšně! Nyní se můžete přihlásit.";
            }
        } catch (PDOException $e) {
            $error = "Chyba při registraci: Zkuste to prosím znovu.";
        }
    }
}
?>

    <div class="detail-sekce">
        <div class="detail-wrapper">
            <span class="kategorie-tag">Nový účet</span>
            <h1>Vytvořit účet</h1>

            <?php if ($error): ?>
                <p style="color: #e74c3c; margin-bottom: 15px; font-weight: bold; background: #fdf2f2; padding: 10px; border-radius: 5px; border-left: 5px solid #e74c3c;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p style="color: #27ae60; margin-bottom: 15px; font-weight: bold; background: #f2fdf5; padding: 10px; border-radius: 5px; border-left: 5px solid #27ae60;">
                    <?php echo $success; ?>
                </p>
                <a href="login.php" class="btn-vlozit" style="display: block; text-align: center; text-decoration: none;">Přejít k přihlášení</a>
            <?php else: ?>
                <form action="registrace.php" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Jméno:</label>
                        <input type="text" name="jmeno" value="<?php echo isset($jmeno) ? htmlspecialchars($jmeno) : ''; ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Příjmení:</label>
                        <input type="text" name="prijmeni" value="<?php echo isset($prijmeni) ? htmlspecialchars($prijmeni) : ''; ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
                        <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>
                    <div style="margin-bottom: 25px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Heslo (min. 6 znaků):</label>
                        <input type="password" name="heslo" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                    </div>

                    <button type="submit" class="btn-vlozit">Zaregistrovat se</button>
                </form>
            <?php endif; ?>

            <p style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                Už máte účet? <a href="login.php" style="color: #222b31; font-weight: bold;">Přihlaste se</a>
            </p>
        </div>
    </div>

<?php include_once 'templates/footer.php'; ?>