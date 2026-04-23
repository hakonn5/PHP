<?php
declare(strict_types=1);

/**
 * Класс для выполнения бизнес-логики над транзакциями.
 */
class TransactionManager
{
    /**
     * @param TransactionStorageInterface $repository Репозиторий транзакций
     */
    public function __construct(
        private TransactionStorageInterface $repository
    ) {}

    /**
     * Вычисляет общую сумму всех транзакций.
     * @return float Общая сумма
     */
    public function calculateTotalAmount(): float
    {
        $total = 0.0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }
        return $total;
    }

    /**
     * Вычисляет общую сумму транзакций в заданном диапазоне дат.
     * @param string $startDate Начальная дата (Y-m-d)
     * @param string $endDate Конечная дата (Y-m-d)
     * @return float Общая сумма за период
     */
    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $total = 0.0;
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $txDate = strtotime($transaction->getDate());
            if ($txDate >= $start && $txDate <= $end) {
                $total += $transaction->getAmount();
            }
        }
        return $total;
    }

    /**
     * Подсчитывает количество транзакций для конкретного получателя.
     * @param string $merchant Имя получателя
     * @return int Количество транзакций
     */
    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;
        foreach ($this->repository->getAllTransactions() as $transaction) {
            if (strtolower($transaction->getMerchant()) === strtolower($merchant)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Возвращает список транзакций, отсортированный по дате.
     * @return Transaction[] Отсортированный массив
     */
    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();
        usort($transactions, function (Transaction $a, Transaction $b) {
            return strtotime($a->getDate()) <=> strtotime($b->getDate());
        });
        return $transactions;
    }

    /**
     * Возвращает список транзакций, отсортированный по убыванию суммы.
     * @return Transaction[] Отсортированный массив
     */
    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();
        usort($transactions, function (Transaction $a, Transaction $b) {
            return $b->getAmount() <=> $a->getAmount();
        });
        return $transactions;
    }
}