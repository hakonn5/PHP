<?php
declare(strict_types=1);

require_once 'TransactionStorageInterface.php';
require_once 'Transaction.php';
require_once 'TransactionRepository.php';
require_once 'TransactionManager.php';
require_once 'TransactionTableRenderer.php';

$repository = new TransactionRepository();

$repository->addTransaction(new Transaction(1, '2023-10-01', 150.50, 'Покупка продуктов', 'SuperMart'));
$repository->addTransaction(new Transaction(2, '2023-10-05', 45.00, 'Кофе', 'Starbucks'));
$repository->addTransaction(new Transaction(3, '2023-10-10', 1200.00, 'Аренда квартиры', 'RealEstate LLC'));
$repository->addTransaction(new Transaction(4, '2023-10-15', 89.99, 'Подписка на сервисы', 'Netflix'));
$repository->addTransaction(new Transaction(5, '2023-10-20', 350.00, 'Одежда', 'Zara'));
$repository->addTransaction(new Transaction(6, '2023-10-25', 15.00, 'Такси', 'Uber'));
$repository->addTransaction(new Transaction(7, '2023-11-01', 200.00, 'Ужин в ресторане', 'Gusto'));
$repository->addTransaction(new Transaction(8, '2023-11-05', 60.50, 'Билеты в кино', 'CinemaPark'));
$repository->addTransaction(new Transaction(9, '2023-11-10', 900.00, 'Покупка смартфона', 'TechStore'));
$repository->addTransaction(new Transaction(10, '2023-11-15', 30.00, 'Книги', 'BookShop'));

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();
$sortedTransactions = $manager->sortTransactionsByAmountDesc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №5 - Транзакции</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Управление банковскими транзакциями</h1>

    <h2>Все транзакции (отсортированные по убыванию суммы):</h2>
    <?= $renderer->render($sortedTransactions) ?>

    <div class="statistics">
        <h3>Дополнительная статистика:</h3>
        <ul>
            <li><b>Общая сумма всех транзакций:</b> <?= $manager->calculateTotalAmount() ?> $</li>
            <li><b>Сумма транзакций за октябрь:</b> <?= $manager->calculateTotalAmountByDateRange('2023-10-01', '2023-10-31') ?> $</li>
            <li><b>Количество визитов в Starbucks:</b> <?= $manager->countTransactionsByMerchant('Starbucks') ?></li>
        </ul>
    </div>
</body>
</html>