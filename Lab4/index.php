<?php

declare(strict_types=1);

/**
 * @var array $transactions
 */
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ]
];

/**
 * Вычисляет общую сумму всех транзакций.
 *
 * @param array $transactions Массив транзакций
 * @return float Общая сумма
 */
function calculateTotalAmount(array $transactions): float {
    $total = 0.0;
    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }
    return $total;
}

/**
 * Ищет транзакцию по части описания.
 *
 * @param string $descriptionPart Часть описания для поиска
 * @return array Массив найденных транзакций
 */
function findTransactionByDescription(string $descriptionPart): array {
    global $transactions;
    return array_filter($transactions, function($transaction) use ($descriptionPart) {
        return str_contains(strtolower($transaction['description']), strtolower($descriptionPart));
    });
}

/**
 * Ищет транзакцию по идентификатору (с использованием foreach).
 *
 * @param int $id Идентификатор транзакции
 * @return array|null Найденная транзакция или null, если не найдена
 */
function findTransactionById(int $id): ?array {
    global $transactions;
    foreach ($transactions as $transaction) {
        if ($transaction['id'] === $id) {
            return $transaction;
        }
    }
    return null;
}

/**
 * Ищет транзакцию по идентификатору (с использованием array_filter).
 *
 * @param int $id Идентификатор транзакции
 * @return array|null Найденная транзакция или null, если не найдена
 */
function findTransactionByIdFilter(int $id): ?array {
    global $transactions;
    $result = array_filter($transactions, function($transaction) use ($id) {
        return $transaction['id'] === $id;
    });
    return !empty($result) ? array_values($result)[0] : null;
}

/**
 * Возвращает количество дней между датой транзакции и текущим днем.
 *
 * @param string $date Дата транзакции в формате YYYY-MM-DD
 * @return int Количество дней
 */
function daysSinceTransaction(string $date): int {
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($transactionDate);
    return (int)$interval->days;
}

/**
 * Добавляет новую транзакцию в глобальный массив.
 *
 * @param int $id Уникальный идентификатор
 * @param string $date Дата (YYYY-MM-DD)
 * @param float $amount Сумма
 * @param string $description Описание
 * @param string $merchant Название организации
 * @return void
 */
function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void {
    global $transactions;
    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}

addTransaction(3, "2023-11-01", 50.00, "Coffee beans", "Cafe Beans");

$transactionsByDate = $transactions;
usort($transactionsByDate, function($a, $b) {
    return strtotime($a['date']) <=> strtotime($b['date']);
});

$transactionsByAmount = $transactions;
usort($transactionsByAmount, function($a, $b) {
    return $b['amount'] <=> $a['amount']; 
});

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Управление банковскими транзакциями</h1>
    </header>

    <main>
        <h2>Список транзакций</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Сумма ($)</th>
                    <th>Описание</th>
                    <th>Организация</th>
                    <th>Дней прошло</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactionsByDate as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)$transaction['id']) ?></td>
                        <td><?= htmlspecialchars($transaction['date']) ?></td>
                        <td><?= htmlspecialchars((string)$transaction['amount']) ?></td>
                        <td><?= htmlspecialchars($transaction['description']) ?></td>
                        <td><?= htmlspecialchars($transaction['merchant']) ?></td>
                        <td><?= daysSinceTransaction($transaction['date']) ?> дней</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align: right;">Общая сумма:</th>
                    <th colspan="4"><?= calculateTotalAmount($transactionsByDate) ?> $</th>
                </tr>
            </tfoot>
        </table>

        <hr>

        <h2>Галерея изображений</h2>
        <div class="gallery">
            <?php
            $dir = 'image/';
            
            if (is_dir($dir)) {
                $files = scandir($dir);
                if ($files !== false) {
                    for ($i = 0; $i < count($files); $i++) {
                        if ($files[$i] !== "." && $files[$i] !== ".." && pathinfo($files[$i], PATHINFO_EXTENSION) === 'jpg') {
                            $path = $dir . $files[$i];
                            echo "<img src=\"" . htmlspecialchars($path) . "\" alt=\"Галерея\">";
                        }
                    }
                }
            } else {
                echo "<p><em>Папка 'image/' не найдена. Пожалуйста, создайте директорию с таким именем рядом с файлом index.php и поместите туда картинки .jpg.</em></p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p class="footer"><b>USM - 2026</b></p>
    </footer>

</body>
</html>