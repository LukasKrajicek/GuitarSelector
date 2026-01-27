<?php
require_once 'db_connect.php';
session_start(); // Startujeme session - důležité!

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $heslo = $_POST['heslo'];

    $sql = "SELECT * FROM uzivatele WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $uzivatel = $stmt->fetch();

    // password_verify porovná zadané heslo s hashem v DB
    if ($uzivatel && password_verify($heslo, $uzivatel['heslo'])) {
        $_SESSION['uzivatel_id'] = $uzivatel['id'];
        $_SESSION['uzivatel_jmeno'] = $uzivatel['jmeno'];

        header("Location: index.php"); // Po přihlášení šup na hlavní stranu
        exit;
    } else {
        $error = "Špatný email nebo heslo!";
    }
}
include_once 'templates/header.php';
?>

<div class="detail-sekce">
    <div class="detail-wrapper">
        <span class="kategorie-tag">Přihlášení</span>
        <h1>Vítejte zpět</h1>

        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Váš email" required style="width:100%; padding:12px; margin-bottom:15px; border-radius:8px; border:1px solid #ddd;">
            <input type="password" name="heslo" placeholder="Heslo" required style="width:100%; padding:12px; margin-bottom:20px; border-radius:8px; border:1px solid #ddd;">
            <button type="submit" class="btn-vlozit">Přihlásit se</button>
        </form>
        <p style="margin-top:20px; text-align:center;">Nemáte účet? <a href="registrace.php">Zaregistrujte se</a></p>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>
