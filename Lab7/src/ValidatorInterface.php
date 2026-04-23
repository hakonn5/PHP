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