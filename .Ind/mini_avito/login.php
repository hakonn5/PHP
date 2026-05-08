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
    } else {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_banned'] == 1) {
                $error = 'Ваш аккаунт заблокирован администратором.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];
                header('Location: index.php');
                exit;
            }
        } else {
            $error = 'Неверное имя пользователя или пароль.';
        }
    }
}
?>

<div class="card" style="max-width: 400px; margin: 0 auto; margin-top: 50px;">
    <h2 style="margin-top: 0;">Вход в аккаунт</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <div class="form-group">
            <label for="username">Имя пользователя</label>
            <input type="text" id="username" name="username" value="<?= sanitizeInput($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%; font-size: 16px; padding: 10px;">Войти</button>
    </form>
    <p style="text-align: center; margin-top: 15px; font-size: 14px;">
        Нет аккаунта? <a href="register.php" style="color: var(--primary); text-decoration: none;">Зарегистрироваться</a>
    </p>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
