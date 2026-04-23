<?php
declare(strict_types=1);

require_once 'Validators.php';

$dataFile = 'data.json';
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$successMessage = '';
$errorMessages = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $startDate = htmlspecialchars(trim($_POST['start_date'] ?? ''));
    $category = htmlspecialchars(trim($_POST['category'] ?? ''));
    $isDaily = isset($_POST['is_daily']);

    // Применение интерфейса валидаторов (Задание 1)
    $reqVal = new RequiredValidator();
    $minLenVal = new MinLengthValidator(3);

    if ($err = $reqVal->validate($name) ?? $minLenVal->validate($name)) {
        $errorMessages[] = "Название: " . $err;
    }
    if ($err = $reqVal->validate($description)) {
        $errorMessages[] = "Описание: " . $err;
    }
    if ($err = $reqVal->validate($startDate)) {
        $errorMessages[] = "Дата начала: " . $err;
    }

    if (empty($errorMessages)) {
        $habit = [
            'id' => uniqid('hab_'),
            'name' => $name,
            'description' => $description,
            'start_date' => $startDate,
            'category' => $category,
            'is_daily' => $isDaily,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $json = file_get_contents($dataFile);
        $data = json_decode($json, true) ?: [];
        $data[] = $habit;
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $successMessage = "Привычка успешно добавлена!";
    }
}

$json = file_get_contents($dataFile);
$habits = json_decode($json, true) ?: [];
$sortColumn = $_GET['sort'] ?? 'created_at';

usort($habits, function($a, $b) use ($sortColumn) {
    if ($sortColumn === 'name') {
        return strcmp($a['name'], $b['name']);
    } elseif ($sortColumn === 'category') {
        return strcmp($a['category'], $b['category']);
    }
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер привычек</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Добавить новую привычку</h1>

    <?php if ($successMessage): ?>
        <div class="success-msg"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="error-msg">
            <ul>
                <?php foreach ($errorMessages as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div class="form-group">
            <label for="name">Название привычки (string)</label>
            <input type="text" id="name" name="name" required minlength="3" placeholder="Например: Читать 2 страницы в день">
        </div>

        <div class="form-group">
            <label for="description">Описание / Мотивация (text)</label>
            <textarea id="description" name="description" required placeholder="Зачем мне это нужно?"></textarea>
        </div>

        <div class="form-group">
            <label for="start_date">Дата начала (date)</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <div class="form-group">
            <label for="category">Категория (enum)</label>
            <select id="category" name="category" required>
                <option value="Здоровье">Здоровье</option>
                <option value="Обучение">Обучение</option>
                <option value="Спорт">Спорт</option>
                <option value="Финансы">Финансы</option>
            </select>
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" id="is_daily" name="is_daily" value="1">
            <label for="is_daily" style="margin: 0;">Выполнять ежедневно? (checkbox)</label>
        </div>

        <button type="submit">Сохранить привычку</button>
    </form>

    <hr style="margin: 40px 0;">

    <h2>Мои привычки</h2>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=name">Название ↕</a></th>
                <th>Описание</th>
                <th><a href="?sort=category">Категория ↕</a></th>
                <th>Дата старта</th>
                <th>Ежедневно?</th>
                <th><a href="?sort=created_at">Добавлено ↕</a></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($habits)): ?>
                <tr><td colspan="6" style="text-align: center;">Список пока пуст. Создайте первую привычку!</td></tr>
            <?php else: ?>
                <?php foreach ($habits as $habit): ?>
                    <tr>
                        <td><?= htmlspecialchars($habit['name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($habit['description'])) ?></td>
                        <td><?= htmlspecialchars($habit['category']) ?></td>
                        <td><?= htmlspecialchars($habit['start_date']) ?></td>
                        <td><?= $habit['is_daily'] ? '✅ Да' : '❌ Нет' ?></td>
                        <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($habit['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>