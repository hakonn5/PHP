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