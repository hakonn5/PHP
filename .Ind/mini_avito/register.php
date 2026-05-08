<?php
require_once __DIR__ . '/header.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля.';
    } elseif (mb_strlen($username) < 3) {
        $error = 'Имя пользователя должно содержать не менее 3 символов.';
    } else {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким именем уже существует.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);
            
            // Авторизация после успешной регистрации
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = 0;
            
            header('Location: index.php');
            exit;
        }
    }
}
?>

<div class="card" style="max-width: 400px; margin: 0 auto; margin-top: 50px;">
    <h2 style="margin-top: 0;">Регистрация</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <div class="form-group">
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username" value="<?= sanitizeInput($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%; font-size: 16px; padding: 10px;">Зарегистрироваться</button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        Уже есть аккаунт? <a href="login.php" style="color: var(--primary); text-decoration: none;">Войти</a>
    </p>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
