<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini-Avito</title>
    <style>
        :root {
            --primary: #00AAFF;
            --primary-dark: #0088CC;
            --bg: #f4f5f7;
            --text: #333;
            --card-bg: #fff;
            --border: #ddd;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: var(--card-bg);
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 15px;
            width: 100%;
            box-sizing: border-box;
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            margin-left: 15px;
            transition: color 0.2s;
        }
        .nav .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
            margin-left: 0;
        }
        .nav a:hover {
            color: var(--primary);
        }
        .btn {
            background-color: var(--primary);
            color: #fff !important;
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
            font-size: 14px;
        }
        .btn:hover {
            background-color: var(--primary-dark);
        }
        .content {
            padding: 30px 0;
            flex-grow: 1;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .error { color: #d9534f; background: #fdf7f7; padding: 10px; border: 1px solid #d9534f; border-radius: 4px; margin-bottom: 15px; }
        .success { color: #5cb85c; background: #f4fdf4; padding: 10px; border: 1px solid #5cb85c; border-radius: 4px; margin-bottom: 15px; }
        .ad-meta { font-size: 0.9em; color: #777; margin-bottom: 10px; }
        .ad-price { font-size: 1.2em; font-weight: bold; color: var(--primary); margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid var(--border); text-align: left; }
        th { background-color: #f9f9f9; font-weight: 600; color: #444; }
        tr:hover { background-color: #fcfcfc; }
    </style>
</head>
<body>
    <header>
        <div class="container nav">
            <div>
                <a href="index.php" class="logo">Mini-Avito</a>
            </div>
            <div>
                <?php if (isLoggedIn()): ?>
                    <a href="create_ad.php" class="btn">Разместить объявление</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Админ-панель</a>
                    <?php endif; ?>
                    <a href="logout.php">Выйти (<?= sanitizeInput($_SESSION['username'] ?? '') ?>)</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                    <a href="register.php" class="btn">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="container content">
