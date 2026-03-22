# Лабораторная работа №5. Объектно-ориентированное программирование в PHP

## 1. Модульная структура проекта

Для соблюдения стандартов профессиональной разработки проект разделен на независимые модули. Каждый класс, интерфейс и файл стилей вынесены отдельно.

**Файловая структура:**

- `style.css` — визуальное оформление (отделено от логики).
- `TransactionStorageInterface.php` — интерфейс (контракт) хранилища.
- `Transaction.php` — класс-сущность транзакции.
- `TransactionRepository.php` — класс для хранения данных.
- `TransactionManager.php` — класс бизнес-логики (вычисления, фильтрация).
- `TransactionTableRenderer.php` — класс для генерации HTML-представления.
- `index.php` — точка входа (сборка модулей и запуск приложения).

### Модуль 1: style.css

```css
/* style.css */
body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background-color: #fcfcfc;
}
.transaction-table {
  border-collapse: collapse;
  width: 100%;
  text-align: left;
  background-color: #ffffff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.transaction-table th,
.transaction-table td {
  border: 1px solid #ddd;
  padding: 12px;
}
.transaction-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}
.transaction-table tr:hover {
  background-color: #f9f9f9;
}
.statistics {
  background-color: #eef2f5;
  padding: 15px;
  border-radius: 5px;
  margin-top: 20px;
}
```

### Модуль 2: TransactionStorageInterface.php

```php
<?php
declare(strict_types=1);

interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}
```

### Модуль 3: Transaction.php

```php
<?php
declare(strict_types=1);

class Transaction
{
    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant
    ) {}

    public function getId(): int { return $this->id; }
    public function getDate(): string { return $this->date; }
    public function getAmount(): float { return $this->amount; }
    public function getDescription(): string { return $this->description; }
    public function getMerchant(): string { return $this->merchant; }

    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();
        return (int)$currentDate->diff($transactionDate)->days;
    }
}
```

### Модуль 4: TransactionRepository.php

```php
<?php
declare(strict_types=1);

class TransactionRepository implements TransactionStorageInterface
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[$transaction->getId()] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        if (isset($this->transactions[$id])) {
            unset($this->transactions[$id]);
        }
    }

    public function getAllTransactions(): array
    {
        return array_values($this->transactions);
    }

    public function findById(int $id): ?Transaction
    {
        return $this->transactions[$id] ?? null;
    }
}
```

### Модуль 5: TransactionManager.php

```php
<?php
declare(strict_types=1);

class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {}

    public function calculateTotalAmount(): float
    {
        $total = 0.0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }
        return $total;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $total = 0.0;
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $txDate = strtotime($transaction->getDate());
            if ($txDate >= $start && $txDate <= $end) {
                $total += $transaction->getAmount();
            }
        }
        return $total;
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            if (strtolower($transaction->getMerchant()) === strtolower($merchant)) {
                $count++;
            }
        }
        return $count;
    }

    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();
        usort($transactions, function (Transaction $a, Transaction $b) {
            return strtotime($a->getDate()) <=> strtotime($b->getDate());
        });
        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();
        usort($transactions, function (Transaction $a, Transaction $b) {
            return $b->getAmount() <=> $a->getAmount();
        });
        return $transactions;
    }
}
```

### Модуль 6: TransactionTableRenderer.php

```php
<?php
declare(strict_types=1);

final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        // Используем CSS-класс вместо inline-стилей
        $html = "<table class='transaction-table'>";
        $html .= "<thead><tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                    <th>Получатель</th>
                    <th>Категория</th>
                    <th>Дней прошло</th>
                  </tr></thead>";
        $html .= "<tbody>";

        foreach ($transactions as $transaction) {
            $html .= "<tr>";
            $html .= "<td>" . $transaction->getId() . "</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getDate()) . "</td>";
            $html .= "<td>" . number_format($transaction->getAmount(), 2) . " $</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getDescription()) . "</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getMerchant()) . "</td>";
            $html .= "<td>Общая категория</td>";
            $html .= "<td>" . $transaction->getDaysSinceTransaction() . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }
}
```

### Модуль 7: index.php (Точка сборки)

```php
<?php
declare(strict_types=1);

// Подключаем все необходимые модули
require_once 'TransactionStorageInterface.php';
require_once 'Transaction.php';
require_once 'TransactionRepository.php';
require_once 'TransactionManager.php';
require_once 'TransactionTableRenderer.php';

// Инициализация объектов
$repository = new TransactionRepository();

// Наполнение начальными данными
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
```

## 2. Контрольные вопросы

**1. Зачем нужна строгая типизация в PHP и как она помогает при разработке?**
Строгая типизация (`declare(strict_types=1);`) заставляет PHP четко следить за тем, какие типы данных (числа, строки, массивы) передаются в функции и возвращаются из них. Это защищает от скрытых логических ошибок, делает поведение программы предсказуемым и заставляет писать более надежный код.

**2. Что такое класс в объектно-ориентированном программировании и какие основные компоненты класса вы знаете?**
Класс — это "шаблон", по которому создаются конкретные объекты. Основные компоненты класса:

- **Свойства (Properties):** Переменные внутри класса, которые хранят состояние (например, ID или сумма).
- **Методы (Methods):** Функции внутри класса, которые определяют поведение (например, расчет дней).
- **Конструктор (Constructor):** Метод, который автоматически вызывается при создании нового объекта для задания начальных настроек.
- **Модификаторы доступа:** `public`, `private`, `protected`, определяющие, кто может изменять данные внутри класса.

**3. Объясните, что такое полиморфизм и как он может быть реализован в PHP.**
Полиморфизм — это способность программы обрабатывать объекты разных классов так, как будто они принадлежат к одному типу, если у них есть одинаковый набор методов. В PHP это реализуется через интерфейсы, позволяя взаимозаменяемо использовать разные классы (например, разные способы оплаты), если они реализуют один и тот же интерфейс.

**4. Что такое интерфейс в PHP и как он отличается от абстрактного класса?**
Интерфейс — это строгий контракт, который содержит только названия методов, но не их код. Класс может реализовывать несколько интерфейсов сразу. Абстрактный класс — это частично готовый класс, который может содержать как пустые, так и реализованные методы, но наследоваться класс может только от одного абстрактного класса.

**5. Какие преимущества дает использование интерфейсов при проектировании архитектуры приложения? Объясните на примере данной лабораторной работы.**
Интерфейсы делают архитектуру гибкой и слабо связанной. В нашей работе `TransactionManager` опирается на интерфейс `TransactionStorageInterface`, а не на конкретный класс массива. Это значит, что если в будущем мы захотим хранить транзакции в базе данных MySQL, нам достаточно будет создать новый класс `DatabaseRepository`, реализующий этот же интерфейс, и код менеджера при этом менять вообще не придется.
