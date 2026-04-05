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