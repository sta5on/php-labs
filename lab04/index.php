<?php

declare(strict_types=1);

/**
 * @var array<int, array{
 *     id:int,
 *     date:string,
 *     amount:float,
 *     description:string,
 *     merchant:string
 * }> $transactions
 */
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

/**
 * Вычисляет общую сумму всех транзакций
 *
 * @param array<int, array{id:int,date:string,amount:float,description:string,merchant:string}> $transactions
 * @return float
 */
function calculateTotalAmount(array $transactions): float
{
    $totalAmount = 0.00;
    foreach ($transactions as $tx) {
        $totalAmount = (float)$totalAmount + $tx['amount'];
    }

    return $totalAmount;
}

/**
 * Ищет первую транзакцию по совпадению описания
 *
 * @param string $descriptionPart Текст для поиска в поле description
 * @return array{id:int,date:string,amount:float,description:string,merchant:string}|null
 */
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

/**
 * Ищет первую транзакцию по идентификатору через foreach
 *
 * @param int $id Айди транзакции
 * @return array{id:int,date:string,amount:float,description:string,merchant:string}|null
 */
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

/**
 * Ищет транзакцию по идентификатору через array_filter
 *
 * @param int $id Id транзакции
 * @return array<int, array{id:int,date:string,amount:float,description:string,merchant:string}>|null
 */
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

/**
 * Возвращает количество дней между датой транзакции и сегодняшним днем
 *
 * @param string $date Дата в формате YYYY-MM-DD
 * @return int
 */
function daysSinceTransaction(string $date): int
{
    return (int)((strtotime('today') - strtotime($date)) / 86400);
}

/**
 * Возвращает следующий свободный идентификатор транзакции
 *
 * @param array<int, array{id:int,date:string,amount:float,description:string,merchant:string}> $transactions
 * @return int
 */
function getNextTransId(array $transactions): int
{
    if ($transactions === []) {
        return 1;
    }
    $ids = array_column($transactions, 'id');
    return max($ids) + 1;
}

/**
 * Добавляет новую транзакцию в глобальный массив $transactions
 *
 * @param int $id Уникальный идентификатор транзакции
 * @param string $date Дата транзакции в формате YYYY-MM-DD
 * @param float $amount Сумма транзакции
 * @param string $description Описание транзакции
 * @param string $merchant Получатель платежа
 * @return void
 */
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

/**
 * Рендерит HTML-таблицу с транзакциями и итоговой суммой
 *
 * @param array<int, array{id:int,date:string,amount:float,description:string,merchant:string}> $items
 * @param string $title Заголовок таблицы
 * @return void
 */
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
