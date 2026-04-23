<?php
declare(strict_types=1);

/**
 * Класс, описывающий банковскую транзакцию.
 */
class Transaction
{
    /**
     * @param int $id Уникальный идентификатор
     * @param string $date Дата транзакции (Y-m-d)
     * @param float $amount Сумма транзакции
     * @param string $description Описание платежа
     * @param string $merchant Получатель платежа
     */
    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant
    ) {}

    /** @return int */
    public function getId(): int { return $this->id; }
    /** @return string */
    public function getDate(): string { return $this->date; }
    /** @return float */
    public function getAmount(): float { return $this->amount; }
    /** @return string */
    public function getDescription(): string { return $this->description; }
    /** @return string */
    public function getMerchant(): string { return $this->merchant; }

    /**
     * Возвращает количество дней, прошедших с момента транзакции.
     * @return int Количество дней
     */
    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();
        return (int)$currentDate->diff($transactionDate)->days;
    }
}