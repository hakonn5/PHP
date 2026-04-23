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
    <div style="margin-bottom: 20px; text-align: right;">
        <strong>Движок:</strong>
        <?php $currentEngine = $_GET['engine'] ?? 'native'; ?>
        <?php if ($currentEngine === 'native'): ?>
            <span>Native PHP</span>
        <?php else: ?>
            <a href="?engine=native">Native PHP</a>
        <?php endif; ?>
        |
        <?php if ($currentEngine === 'twig'): ?>
            <span>Twig</span>
        <?php else: ?>
            <a href="?engine=twig">Twig</a>
        <?php endif; ?>
    </div>

    <h1>Добавить новую привычку</h1>

    <?php if (!empty($successMessage)): ?>
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

    <?php include 'form.php'; ?>

    <hr style="margin: 40px 0;">

    <h2>Мои привычки</h2>
    <?php include 'list.php'; ?>
</div>

</body>
</html>
