<?php
require_once __DIR__ . '/header.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$pdo = getDbConnection();

// Обработка действий (удаление объявления, бан/разбан пользователя)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete_ad') {
        $adId = filter_var($_POST['ad_id'] ?? 0, FILTER_VALIDATE_INT);
        if ($adId) {
            $stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
            $stmt->execute([$adId]);
        }
    } elseif ($action === 'toggle_ban') {
        $userId = filter_var($_POST['user_id'] ?? 0, FILTER_VALIDATE_INT);
        if ($userId && $userId != getCurrentUserId()) { // Нельзя забанить самого себя
            $stmt = $pdo->prepare("UPDATE users SET is_banned = CASE WHEN is_banned = 1 THEN 0 ELSE 1 END WHERE id = ?");
            $stmt->execute([$userId]);
        }
    }
    
    // Редирект для предотвращения повторной отправки формы (PRG)
    header('Location: admin.php');
    exit;
}

// Получение всех пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// Получение всех объявлений
$stmt = $pdo->query("
    SELECT ads.*, users.username, categories.name as category_name 
    FROM ads 
    JOIN users ON ads.user_id = users.id 
    JOIN categories ON ads.category_id = categories.id 
    ORDER BY ads.created_at DESC
");
$ads = $stmt->fetchAll();
?>

<h2>Панель управления администратора</h2>

<div class="card">
    <h3>Управление пользователями</h3>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><strong><?= sanitizeInput($user['username']) ?></strong></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <span style="background-color: var(--primary); color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.85em;">Админ</span>
                            <?php else: ?>
                                <span style="color: #666;">Пользователь</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['is_banned']): ?>
                                <span style="color: #d9534f; font-weight: bold;">Забанен</span>
                            <?php else: ?>
                                <span style="color: #5cb85c;">Активен</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['id'] != getCurrentUserId()): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle_ban">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn" style="background-color: <?= $user['is_banned'] ? '#5cb85c' : '#d9534f' ?>; padding: 5px 10px; font-size: 12px;">
                                        <?= $user['is_banned'] ? 'Разбанить' : 'Забанить' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">Это вы</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h3>Управление объявлениями</h3>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Автор</th>
                    <th>Цена</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ads as $ad): ?>
                    <tr>
                        <td><?= $ad['id'] ?></td>
                        <td><?= sanitizeInput($ad['title']) ?></td>
                        <td><?= sanitizeInput($ad['category_name']) ?></td>
                        <td><?= sanitizeInput($ad['username']) ?></td>
                        <td style="font-weight: bold;"><?= number_format($ad['price'], 2, '.', ' ') ?> ₽</td>
                        <td><?= date('d.m.Y H:i', strtotime($ad['created_at'])) ?></td>
                        <td>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите безвозвратно удалить это объявление?');">
                                <input type="hidden" name="action" value="delete_ad">
                                <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
                                <button type="submit" class="btn" style="background-color: #d9534f; padding: 5px 10px; font-size: 12px;">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
