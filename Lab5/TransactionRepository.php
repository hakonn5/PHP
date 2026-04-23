<?php
declare(strict_types=1);

/**
 * Репозиторий для хранения транзакций в оперативной памяти.
 */
class TransactionRepository implements TransactionStorageInterface
{
    /** @var Transaction[] Массив транзакций */
    private array $transactions = [];

    /**
     * @inheritDoc
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[$transaction->getId()] = $transaction;
    }

    /**
     * @inheritDoc
     */
    public function removeTransactionById(int $id): void
    {
        if (isset($this->transactions[$id])) {
            unset($this->transactions[$id]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAllTransactions(): array
    {
        return array_values($this->transactions);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Transaction
    {
        return $this->transactions[$id] ?? null;
    }
}