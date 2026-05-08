<?php
require_once __DIR__ . '/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (isUserBanned(getCurrentUserId())) {
    echo '<div class="card" style="max-width: 600px; margin: 0 auto;">';
    echo '<div class="error">Ваш аккаунт заблокирован. Вы не можете создавать объявления.</div>';
    echo '</div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $categoryId = filter_var($_POST['category_id'] ?? 0, FILTER_VALIDATE_INT);
    $phone = sanitizeInput($_POST['phone'] ?? '');

    if (empty($title) || empty($description) || empty($phone)) {
        $error = 'Пожалуйста, заполните все текстовые поля.';
    } elseif ($price === false || $price < 0) {
        $error = 'Пожалуйста, введите корректную цену.';
    } elseif ($categoryId === false || $categoryId <= 0) {
        $error = 'Пожалуйста, выберите категорию.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, category_id, title, description, price, phone) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([getCurrentUserId(), $categoryId, $title, $description, $price, $phone])) {
            $success = 'Объявление успешно создано!';
            $_POST = []; // Очистка формы после успешного добавления
        } else {
            $error = 'Произошла ошибка при создании объявления.';
        }
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0;">Разместить объявление</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="post" action="create_ad.php">
        <div class="form-group">
            <label for="title">Название товара или услуги</label>
            <input type="text" id="title" name="title" value="<?= sanitizeInput($_POST['title'] ?? '') ?>" required maxlength="255">
        </div>
        <div class="form-group">
            <label for="category_id">Категория</label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Выберите категорию --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= sanitizeInput($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price">Цена (₽)</label>
            <input type="number" step="0.01" min="0" id="price" name="price" value="<?= sanitizeInput($_POST['price'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Контактный телефон</label>
            <input type="text" id="phone" name="phone" value="<?= sanitizeInput($_POST['phone'] ?? '') ?>" required placeholder="+7 (999) 000-00-00">
        </div>
        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description" name="description" rows="5" required><?= sanitizeInput($_POST['description'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn" style="width: 100%; font-size: 16px; padding: 10px;">Создать объявление</button>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
