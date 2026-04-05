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