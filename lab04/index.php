<?php

declare(strict_types=1);

$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
];

function calculateTotalAmount(array $transactions): float
{
    $totalAmount = 0.00;
    foreach ($transactions as $tx) {
        $totalAmount = (float)$totalAmount + $tx['amount'];
    }

    return $totalAmount;
}

function findTransactionByDescription(string $descriptionPart): ?array
{
    global $transactions;
    foreach ($transactions as $tx) {
        if ($tx['description'] === $descriptionPart) {
            return $tx;
        }
    }

    return null;
}

function findTransactionById(int $id): ?array
{
    global $transactions;
    foreach ($transactions as $tx) {
        if ($tx['id'] === $id) {
            return $tx;
        }
    }

    return null;
}

function findTransactionByIdV2(int $id): ?array
{
    global $transactions;

    $filtered = array_filter(
        $transactions,
        static fn(array $tx): bool => $tx['id'] === $id
    );

    if ($filtered === []) {
        return null;
    }

    return $filtered;
}

function daysSinceTransaction(string $date): int
{
    return (int)((strtotime('today') - strtotime($date)) / 86400);
}

function getNextTransId(array $transactions): int
{
    if ($transactions === []) {
        return 1;
    }
    $ids = array_column($transactions, 'id');
    return max($ids) + 1;
}

function addTransaction(int $id, string $date, float $amount, string $description, string $merchant): void
{
    global $transactions;
    $transactions[] = [
        'id' => $id,
        'date' => $date,
        'amount' => $amount,
        'description' => $description,
        'merchant' => $merchant,
    ];
}

function renderTransactionsTable(array $items, string $title): void
{
    echo "<h2>$title</h2>";
    echo "<table border='5' cellpadding='7' cellspacing='0'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Date</th>";
    echo "<th>Amount</th>";
    echo "<th>Description</th>";
    echo "<th>Merchant</th>";
    echo "<th>Days since transaction</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($items as $tx) {
        echo "<tr>";
        echo "<td>{$tx['id']}</td>";
        echo "<td>{$tx['date']}</td>";
        echo "<td>" . (float)$tx['amount'] . "</td>";
        echo "<td>{$tx['description']}</td>";
        echo "<td>{$tx['merchant']}</td>";
        echo "<td>" . daysSinceTransaction($tx['date']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "<tfoot>";
    echo "<tr>";
    echo "<td colspan='2'><strong>Total amount</strong></td>";
    echo "<td><strong>" . calculateTotalAmount($items) . "</strong></td>";
    echo "<td colspan='3'></td>";
    echo "</tr>";
    echo "</tfoot>";
    echo "</table>";
    echo "<br>";
}

$id = getNextTransId($transactions);
addTransaction($id, '2026-02-15', 100.0, 'Test', 'Shop');

$id = getNextTransId($transactions);
addTransaction($id, '2026-02-22', 200.13, 'Stas', 'Stas shop');

$id = getNextTransId($transactions);
addTransaction($id, '2026-03-01', 200.37, 'Stas', 'Stas shop');

$byDate = $transactions;
usort($byDate, fn($a, $b) => strcmp($a['date'], $b['date']));

$byAmountDesc = $transactions;
usort($byAmountDesc, fn($a, $b) => $b['amount'] <=> $a['amount']);

echo "<h1>Transactions</h1>";

renderTransactionsTable($transactions, '1.4 All Transactions');

renderTransactionsTable($byDate, '1.5 Sort by Date');

renderTransactionsTable($byAmountDesc, '1.5 Sort by Amount (DESC)');
