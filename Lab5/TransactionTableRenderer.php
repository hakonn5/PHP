<?php
declare(strict_types=1);

final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        $html = "<table class='transaction-table'>";
        $html .= "<thead><tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                    <th>Получатель</th>
                    <th>Категория</th>
                    <th>Дней прошло</th>
                  </tr></thead>";
        $html .= "<tbody>";

        foreach ($transactions as $transaction) {
            $html .= "<tr>";
            $html .= "<td>" . $transaction->getId() . "</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getDate()) . "</td>";
            $html .= "<td>" . number_format($transaction->getAmount(), 2) . " $</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getDescription()) . "</td>";
            $html .= "<td>" . htmlspecialchars($transaction->getMerchant()) . "</td>";
            $html .= "<td>Общая категория</td>";
            $html .= "<td>" . $transaction->getDaysSinceTransaction() . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }
}