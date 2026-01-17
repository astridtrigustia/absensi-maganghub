<?php
session_start();

$pass = trim(file_get_contents("admin_pass.txt"));
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = isset($_POST['p']) ? trim($_POST['p']) : "";

    if ($input === $pass) {
        $_SESSION['admin'] = 1;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <div class="login-title">Login Admin</div>

        <?php if ($error): ?>
            <div style="
                background:#ffebeb;
                color:#a30000;
                padding:10px 12px;
                border-left:5px solid #e10000;
                border-radius:8px;
                margin-bottom:20px;
                font-size:14px;
            ">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="password" name="p" placeholder="Password admin" required>
            <button type="submit" class="login-btn">Login</button>
        </form>

    </div>
</div>

</body>
</html>
