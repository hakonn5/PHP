<?php
declare(strict_types=1);

/**
 * Интерфейс для хранилища транзакций.
 */
interface TransactionStorageInterface
{
    /**
     * Добавляет транзакцию.
     * @param Transaction $transaction Объект транзакции
     */
    public function addTransaction(Transaction $transaction): void;

    /**
     * Удаляет транзакцию по ID.
     * @param int $id Идентификатор транзакции
     */
    public function removeTransactionById(int $id): void;

    /**
     * Возвращает все транзакции.
     * @return Transaction[] Массив транзакций
     */
    public function getAllTransactions(): array;

    /**
     * Ищет транзакцию по ID.
     * @param int $id Идентификатор
     * @return Transaction|null Объект транзакции или null
     */
    public function findById(int $id): ?Transaction;
}