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