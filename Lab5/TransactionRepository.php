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