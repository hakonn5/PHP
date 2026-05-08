<?php
require_once __DIR__ . '/header.php';

$pdo = getDbConnection();

// Поиск
$searchQuery = sanitizeInput($_GET['q'] ?? '');
$searchCategoryId = filter_var($_GET['category_id'] ?? 0, FILTER_VALIDATE_INT);

// Получение категорий для фильтра
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Построение запроса для объявлений (только незабаненные пользователи)
$sql = "SELECT ads.*, users.username, categories.name as category_name 
        FROM ads 
        JOIN users ON ads.user_id = users.id 
        JOIN categories ON ads.category_id = categories.id 
        WHERE users.is_banned = 0";
$params = [];

if ($searchQuery) {
    $sql .= " AND (ads.title LIKE ? OR ads.description LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

if ($searchCategoryId) {
    $sql .= " AND ads.category_id = ?";
    $params[] = $searchCategoryId;
}

$sql .= " ORDER BY ads.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ads = $stmt->fetchAll();
?>

<div class="card" style="margin-bottom: 30px; background-color: #fff; padding: 25px;">
    <form method="get" action="index.php" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 2; margin-bottom: 0; min-width: 250px;">
            <label for="q">Поиск по объявлениям</label>
            <input type="text" id="q" name="q" value="<?= $searchQuery ?>" placeholder="Что ищете?">
        </div>
        <div class="form-group" style="flex: 1; margin-bottom: 0; min-width: 200px;">
            <label for="category_id">Категория</label>
            <select id="category_id" name="category_id">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($searchCategoryId == $cat['id']) ? 'selected' : '' ?>>
                        <?= sanitizeInput($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn" style="height: 41px; padding: 0 25px;">Найти</button>
        <?php if ($searchQuery || $searchCategoryId): ?>
            <a href="index.php" class="btn" style="height: 41px; line-height: 41px; padding: 0 15px; background-color: #777;">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

<h2>Свежие объявления</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php if (empty($ads)): ?>
        <p style="grid-column: 1 / -1; color: #777;">Объявления не найдены.</p>
    <?php else: ?>
        <?php foreach ($ads as $ad): ?>
            <div class="card" style="display: flex; flex-direction: column; margin-bottom: 0;">
                <h3 style="margin-top: 0; margin-bottom: 10px; color: var(--primary-dark);"><?= sanitizeInput($ad['title']) ?></h3>
                
                <div class="ad-price" style="margin-top: 0; margin-bottom: 15px; font-size: 1.4em;">
                    <?= number_format($ad['price'], 2, '.', ' ') ?> ₽
                </div>
                
                <p style="flex-grow: 1; margin-bottom: 20px; color: #555; line-height: 1.5;">
                    <?= nl2br(sanitizeInput($ad['description'])) ?>
                </p>
                
                <div style="background-color: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">📞 <?= sanitizeInput($ad['phone']) ?></div>
                    <div class="ad-meta" style="margin-bottom: 0;">👤 Автор: <?= sanitizeInput($ad['username']) ?></div>
                </div>

                <div class="ad-meta" style="margin-bottom: 0; display: flex; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 10px;">
                    <span>📁 <?= sanitizeInput($ad['category_name']) ?></span>
                    <span>📅 <?= date('d.m.Y', strtotime($ad['created_at'])) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
