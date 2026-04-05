# Лабораторная работа №6. Обработка и валидация форм

## Тема проекта: Трекер привычек

**Модель данных (Habit):**

1. `id` (string) — уникальный идентификатор записи.
2. `name` (string) — название привычки.
3. `description` (text) — подробное описание или мотивация.
4. `start_date` (date) — дата начала трекинга.
5. `category` (enum) — категория (Здоровье, Обучение, Спорт).
6. `is_daily` (boolean/checkbox) — требуется ли ежедневное выполнение (Да/Нет).
7. `created_at` (date) — системная дата создания записи.

## 1. Модульная структура проекта

В проекте используется строгая типизация и объектно-ориентированный подход.

**Файловая структура:**

- `style.css` — стили формы и таблицы.
- `ValidatorInterface.php` — интерфейс для правил валидации.
- `Validators.php` — конкретные классы-валидаторы (Required, MinLength).
- `Habit.php` — DTO (Data Transfer Object) модели привычки.
- `JsonStorage.php` — класс для сохранения и чтения данных из JSON-файла.
- `FormHandler.php` — контроллер, обрабатывающий логику формы.
- `index.php` — точка входа (HTML-форма и вывод таблицы).
- `data.json` — файл базы данных (создается автоматически).

### Модуль 1: style.css

```css
body {
  font-family: Arial, sans-serif;
  background-color: #f4f7f6;
  color: #333;
  margin: 20px;
}
.container {
  max-width: 900px;
  margin: auto;
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}
input[type="text"],
input[type="date"],
select,
textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box; /* Чтобы padding не ломал ширину */
}
textarea {
  resize: vertical;
  height: 80px;
}
.checkbox-group {
  display: flex;
  align-items: center;
  gap: 10px;
}
.checkbox-group input {
  width: auto;
}
button {
  background-color: #28a745;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}
button:hover {
  background-color: #218838;
}
.error-msg {
  color: #dc3545;
  background: #f8d7da;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 15px;
}
.success-msg {
  color: #155724;
  background: #d4edda;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 15px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th,
td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}
th {
  background-color: #f2f2f2;
}
th a {
  color: #333;
  text-decoration: none;
}
th a:hover {
  text-decoration: underline;
}
```

### Модуль 2: ValidatorInterface.php

```php
<?php
declare(strict_types=1);

/**
 * Интерфейс для всех правил валидации.
 * Позволяет унифицировать проверку данных.
 */
interface ValidatorInterface
{
    /**
     * Проверяет значение.
     * @param mixed $value Значение из формы
     * @return string|null Сообщение об ошибке или null, если ошибок нет
     */
    public function validate(mixed $value): ?string;
}
```

### Модуль 3: Validators.php

```php
<?php
declare(strict_types=1);

require_once 'ValidatorInterface.php';

/**
 * Валидатор проверки на обязательное заполнение.
 */
class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value): ?string
    {
        if (empty(trim((string)$value))) {
            return "Поле обязательно для заполнения.";
        }
        return null;
    }
}

/**
 * Валидатор минимальной длины строки.
 */
class MinLengthValidator implements ValidatorInterface
{
    public function __construct(private int $minLength) {}

    public function validate(mixed $value): ?string
    {
        if (mb_strlen(trim((string)$value)) < $this->minLength) {
            return "Минимальная длина поля: {$this->minLength} симв.";
        }
        return null;
    }
}
```

### Модуль 4: Habit.php

```php
<?php
declare(strict_types=1);

/**
 * Класс-сущность, представляющий привычку.
 */
class Habit
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $category,
        public readonly bool $isDaily,
        public readonly string $createdAt
    ) {}

    /**
     * Преобразует объект в ассоциативный массив для сохранения в JSON.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->startDate,
            'category' => $this->category,
            'is_daily' => $this->isDaily,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * Создает объект из массива.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['description'],
            $data['start_date'],
            $data['category'],
            (bool)$data['is_daily'],
            $data['created_at']
        );
    }
}
```

### Модуль 5: JsonStorage.php

```php
<?php
declare(strict_types=1);

require_once 'Habit.php';

/**
 * Класс для работы с файловой системой (чтение и запись JSON).
 */
class JsonStorage
{
    public function __construct(private string $filePath)
    {
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    /**
     * Сохраняет новую привычку в файл.
     */
    public function save(Habit $habit): void
    {
        $data = $this->fetchAllRaw();
        $data[] = $habit->toArray();
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Возвращает массив объектов Habit.
     * @return Habit[]
     */
    public function fetchAll(): array
    {
        $data = $this->fetchAllRaw();
        $habits = [];
        foreach ($data as $item) {
            $habits[] = Habit::fromArray($item);
        }
        return $habits;
    }

    private function fetchAllRaw(): array
    {
        $json = file_get_contents($this->filePath);
        return json_decode($json, true) ?: [];
    }
}
```

### Модуль 6: FormHandler.php

```php
<?php
declare(strict_types=1);

require_once 'Validators.php';
require_once 'Habit.php';
require_once 'JsonStorage.php';

/**
 * Контроллер для обработки POST-запросов и валидации данных.
 */
class FormHandler
{
    private array $errors = [];

    public function __construct(private JsonStorage $storage) {}

    /**
     * Обрабатывает данные формы.
     * @param array $postData Данные из $_POST
     * @return bool Успешно ли прошла обработка
     */
    public function handle(array $postData): bool
    {
        $name = htmlspecialchars(trim($postData['name'] ?? ''));
        $description = htmlspecialchars(trim($postData['description'] ?? ''));
        $startDate = htmlspecialchars(trim($postData['start_date'] ?? ''));
        $category = htmlspecialchars(trim($postData['category'] ?? ''));
        $isDaily = isset($postData['is_daily']);

        // Применение валидаторов
        $reqVal = new RequiredValidator();
        $minLenVal = new MinLengthValidator(3);

        if ($err = $reqVal->validate($name) ?? $minLenVal->validate($name)) {
            $this->errors[] = "Название: " . $err;
        }
        if ($err = $reqVal->validate($description)) {
            $this->errors[] = "Описание: " . $err;
        }
        if ($err = $reqVal->validate($startDate)) {
            $this->errors[] = "Дата начала: " . $err;
        }

        if (empty($this->errors)) {
            $habit = new Habit(
                uniqid('hab_'),
                $name,
                $description,
                $startDate,
                $category,
                $isDaily,
                date('Y-m-d H:i:s')
            );
            $this->storage->save($habit);
            return true;
        }

        return false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

### Модуль 7: index.php (Точка входа и UI)

```php
<?php
declare(strict_types=1);

require_once 'JsonStorage.php';
require_once 'FormHandler.php';

$storage = new JsonStorage('data.json');
$handler = new FormHandler($storage);

$successMessage = '';
$errorMessages = [];

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($handler->handle($_POST)) {
        $successMessage = "Привычка успешно добавлена!";
    } else {
        $errorMessages = $handler->getErrors();
    }
}

// Получение данных и логика сортировки (GET-параметр sort)
$habits = $storage->fetchAll();
$sortColumn = $_GET['sort'] ?? 'created_at';

usort($habits, function(Habit $a, Habit $b) use ($sortColumn) {
    if ($sortColumn === 'name') {
        return strcmp($a->name, $b->name);
    } elseif ($sortColumn === 'category') {
        return strcmp($a->category, $b->category);
    }
    // По умолчанию сортируем по дате создания (новые сверху)
    return strtotime($b->createdAt) <=> strtotime($a->createdAt);
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
                        <td><?= htmlspecialchars($habit->name) ?></td>
                        <td><?= nl2br(htmlspecialchars($habit->description)) ?></td>
                        <td><?= htmlspecialchars($habit->category) ?></td>
                        <td><?= htmlspecialchars($habit->startDate) ?></td>
                        <td><?= $habit->isDaily ? '✅ Да' : '❌ Нет' ?></td>
                        <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($habit->createdAt))) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
```

## Контрольные вопросы

**1. Какие существуют методы отправки данных из формы на сервер? Какие методы поддерживает HTML-форма?**
В протоколе HTTP существует множество методов отправки данных (GET, POST, PUT, DELETE, PATCH и др.). Однако сами **HTML-формы** (через атрибут `method`) нативно поддерживают только два метода:

- **GET** — данные передаются открыто прямо в URL-строке (например, `?name=Ivan&age=20`). Подходит для безопасных поисковых запросов и фильтрации. Имеет ограничение по длине передаваемых данных.
- **POST** — данные передаются скрыто в теле HTTP-запроса (body). Используется для передачи конфиденциальных данных (паролей), загрузки файлов и отправки больших объемов текста. Не имеет строгих ограничений по длине.

**2. Какие глобальные переменные используются для доступа к данным формы в PHP?**
В PHP для приема данных используются суперглобальные массивы:

- `$_GET` — содержит данные, переданные методом GET (или параметры из URL).
- `$_POST` — содержит данные, переданные методом POST.
- `$_REQUEST` — объединяет в себе данные из `$_GET`, `$_POST` и `$_COOKIE` (использовать не рекомендуется из-за риска переопределения переменных).
- `$_FILES` — содержит информацию о загруженных через форму файлах (если форма имеет атрибут `enctype="multipart/form-data"`).

**3. Как обеспечить безопасность при обработке данных из формы (например, защититься от XSS)?**
Главное правило веб-разработки: **никогда не доверять данным от пользователя**.
Для защиты необходимо:

1.  **Защита от XSS (Cross-Site Scripting):** Использовать функцию `htmlspecialchars()` при выводе любых данных от пользователя в HTML. Она преобразует спецсимволы (например, `<` и `>`) в безопасные HTML-сущности (`&lt;` и `&gt;`), не позволяя браузеру исполнить вредоносный JavaScript-код.
2.  **Валидация на сервере:** Всегда проверять типы данных, длину строк и форматы на стороне PHP, так как клиентскую валидацию (HTML5 атрибуты вроде `required`) злоумышленник может легко обойти через инструменты разработчика (F12) или Postman.
3.  **Защита от CSRF (Cross-Site Request Forgery):** Добавление скрытых токенов в форму, которые генерируются сервером и проверяются при отправке, чтобы убедиться, что запрос был отправлен именно с вашего сайта.
