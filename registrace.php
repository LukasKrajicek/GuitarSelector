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

    if (!empty($jmeno) && !empty($prijmeni) && !empty($email) && !empty($heslo)) {
        // 1. Hashování hesla (řešíme připomínku učitele)
        $hashed_heslo = password_hash($heslo, PASSWORD_DEFAULT);

        try {
            // 2. Příprava SQL dotazu
            $sql = "INSERT INTO uzivatele (jmeno, prijmeni, email, heslo) VALUES (:jmeno, :prijmeni, :email, :heslo)";
            $stmt = $pdo->prepare($sql);

            // 3. Spuštění
            $stmt->execute([
                'jmeno' => $jmeno,
                'prijmeni' => $prijmeni,
                'email' => $email,
                'heslo' => $hashed_heslo
            ]);
            $success = "Registrace proběhla úspěšně! Nyní se můžete přihlásit.";
        } catch (PDOException $e) {
            $error = "Chyba při registraci: " . ($e->getCode() == 23000 ? "Tento email už je registrovaný." : "Zkuste to znovu.");
        }
    } else {
        $error = "Všechna pole jsou povinná!";
    }
}
?>

<div class="detail-sekce">
    <div class="detail-wrapper">
        <span class="kategorie-tag">Nový účet</span>
        <h1>Vytvořit účet</h1>

        <?php if ($error): ?>
            <p style="color: #e74c3c; margin-bottom: 15px; font-weight: bold;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color: #27ae60; margin-bottom: 15px; font-weight: bold;"><?php echo $success; ?></p>
        <?php else: ?>
            <form action="registrace.php" method="POST">
                <div style="margin-bottom: 15px;">
                    <label>Jméno:</label>
                    <input type="text" name="jmeno" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Příjmení:</label>
                    <input type="text" name="prijmeni" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Email:</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label>Heslo:</label>
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
