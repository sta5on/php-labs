<?php

declare(strict_types=1);

/**
 * Контракт для хранилища транзакций.
 */
interface TransactionStorageInterface
{
    /**
     * Добавляет транзакцию в хранилище.
     *
     * @param Transaction $transaction Объект транзакции.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void;

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return void
     */
    public function removeTransactionById(int $id): void;

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[]
     */
    public function getAllTransactions(): array;

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null
     */
    public function findById(int $id): ?Transaction;
}

/**
 * Представляет одну банковскую транзакцию.
 */
class Transaction
{
    /**
     * @param int $id Уникальный идентификатор транзакции.
     * @param string $date Дата в формате YYYY-MM-DD.
     * @param float $amount Сумма транзакции.
     * @param string $description Описание платежа.
     * @param string $merchant Получатель платежа.
     * @param string $category Категория получателя.
     */
    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant,
        private string $category
    ) {
    }

    /**
     * Возвращает идентификатор транзакции.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает дату транзакции.
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Возвращает сумму транзакции.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Возвращает описание транзакции.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Возвращает название получателя.
     *
     * @return string
     */
    public function getMerchant(): string
    {
        return $this->merchant;
    }

    /**
     * Возвращает категорию получателя.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Возвращает количество дней с даты транзакции до текущего дня.
     *
     * @return int
     */
    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime('today');
        $difference = $transactionDate->diff($currentDate);

        return (int) $difference->days;
    }
}

/**
 * Хранилище транзакций в памяти.
 */
class TransactionRepository implements TransactionStorageInterface
{
    /**
     * @var Transaction[]
     */
    private array $transactions = [];

    /**
     * Добавляет транзакцию в хранилище.
     *
     * @param Transaction $transaction Объект транзакции.
     * @return void
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Удаляет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return void
     */
    public function removeTransactionById(int $id): void
    {
        $this->transactions = array_values(
            array_filter(
                $this->transactions,
                static fn(Transaction $transaction): bool => $transaction->getId() !== $id
            )
        );
    }

    /**
     * Возвращает все транзакции.
     *
     * @return Transaction[]
     */
    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Ищет транзакцию по идентификатору.
     *
     * @param int $id Идентификатор транзакции.
     * @return Transaction|null
     */
    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }
}

/**
 * Выполняет бизнес-операции над транзакциями.
 */
class TransactionManager
{
    /**
     * @param TransactionStorageInterface $repository Хранилище транзакций.
     */
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    /**
     * Вычисляет общую сумму всех транзакций.
     *
     * @return float
     */
    public function calculateTotalAmount(): float
    {
        $totalAmount = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $totalAmount += $transaction->getAmount();
        }

        return $totalAmount;
    }

    /**
     * Вычисляет сумму транзакций за указанный диапазон дат.
     *
     * @param string $startDate Начальная дата в формате YYYY-MM-DD.
     * @param string $endDate Конечная дата в формате YYYY-MM-DD.
     * @return float
     */
    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $totalAmount = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $transactionDate = new DateTime($transaction->getDate());
            if ($transactionDate >= $start && $transactionDate <= $end) {
                $totalAmount += $transaction->getAmount();
            }
        }

        return $totalAmount;
    }

    /**
     * Подсчитывает количество транзакций для указанного получателя.
     *
     * @param string $merchant Название получателя.
     * @return int
     */
    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            if ($transaction->getMerchant() === $merchant) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Сортирует транзакции по дате по возрастанию.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort(
            $transactions,
            static fn(Transaction $left, Transaction $right): int => strcmp($left->getDate(), $right->getDate())
        );

        return $transactions;
    }

    /**
     * Сортирует транзакции по сумме по убыванию.
     *
     * @return Transaction[]
     */
    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort(
            $transactions,
            static fn(Transaction $left, Transaction $right): int => $right->getAmount() <=> $left->getAmount()
        );

        return $transactions;
    }
}

/**
 * Отвечает за вывод HTML-таблицы транзакций.
 */
final class TransactionTableRenderer
{
    /**
     * Формирует HTML-таблицу транзакций.
     *
     * @param Transaction[] $transactions Список транзакций.
     * @return string
     */
    public function render(array $transactions): string
    {
        $html = '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>ID транзакции</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Сумма</th>';
        $html .= '<th>Описание</th>';
        $html .= '<th>Получатель</th>';
        $html .= '<th>Категория</th>';
        $html .= '<th>Дней с момента транзакции</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . $this->escape((string) $transaction->getId()) . '</td>';
            $html .= '<td>' . $this->escape($transaction->getDate()) . '</td>';
            $html .= '<td>' . $this->escape(number_format($transaction->getAmount(), 2, '.', '')) . '</td>';
            $html .= '<td>' . $this->escape($transaction->getDescription()) . '</td>';
            $html .= '<td>' . $this->escape($transaction->getMerchant()) . '</td>';
            $html .= '<td>' . $this->escape($transaction->getCategory()) . '</td>';
            $html .= '<td>' . $this->escape((string) $transaction->getDaysSinceTransaction()) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    /**
     * Экранирует текст для безопасного HTML-вывода.
     *
     * @param string $value Исходное значение.
     * @return string
     */
    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, '2025-01-10', 125.40, 'bread', 'linella', 'food'),
    new Transaction(2, '2025-01-15', 49.99, 'phone payment', 'orange', 'services'),
    new Transaction(3, '2025-01-25', 18.75, 'card top up', 'lalala', 'Transport'),
    new Transaction(4, '2025-02-03', 220.15, 'electricity', 'electricity', 'services'),
    new Transaction(5, '2025-02-12', 67.30, 'dinner', 'sisters', 'restaurant'),
    new Transaction(6, '2025-02-18', 89.00, 'pharmacy', 'health', 'health'),
    new Transaction(7, '2025-03-01', 310.00, 'course payment', 'usm', 'education'),
    new Transaction(8, '2025-03-05', 42.60, 'Cinema tickets', 'cineplex', 'entertainment'),
    new Transaction(9, '2025-03-09', 154.20, 'groceries', 'linella', 'food'),
    new Transaction(10, '2025-03-14', 95.80, 'Fuel refill', 'rompetrol', 'Transport'),
    new Transaction(11, '2025-03-20', 132.45, 'Water and internet bills', 'services merch', 'services'),
];

foreach ($transactions as $transaction) {
    $repository->addTransaction($transaction);
}

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$allTransactions = $repository->getAllTransactions();
$foundTransaction = $repository->findById(4);
$totalAmount = $manager->calculateTotalAmount();
$dateRangeTotal = $manager->calculateTotalAmountByDateRange('2025-02-01', '2025-03-10');
$merchantTransactionCount = $manager->countTransactionsByMerchant('linella');

$repository->removeTransactionById(3);
$transactionsAfterRemoval = $repository->getAllTransactions();
$sortedByDate = $manager->sortTransactionsByDate();
$sortedByAmountDesc = $manager->sortTransactionsByAmountDesc();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №5</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 24px;">
    <h1>Лабораторная работа №5. Объектно-ориентированное программирование в PHP</h1>

    <h2>Вычисления и поиск</h2>
    <p><strong>Общая сумма всех транзакций:</strong> <?= $totalAmount ?></p>
    <p><strong>Сумма транзакций за период 2025-02-01 - 2025-03-10:</strong> <?= $dateRangeTotal ?></p>
    <p><strong>Количество транзакций для получателя Linella:</strong> <?= $merchantTransactionCount ?></p>
    <p>
        <strong>Поиск транзакции по ID = 4:</strong>
        <?php if ($foundTransaction instanceof Transaction): ?>
            <?= $foundTransaction->getDescription() ?>
            (<?= $foundTransaction->getAmount() ?>)
        <?php else: ?>
            Транзакция не найдена
        <?php endif; ?>
    </p>

    <h2>Все транзакции</h2>
    <?= $renderer->render($allTransactions) ?>

    <h2>Транзакции после удаления ID = 3</h2>
    <?= $renderer->render($transactionsAfterRemoval) ?>

    <h2>Сортировка по дате</h2>
    <?= $renderer->render($sortedByDate) ?>

    <h2>Сортировка по сумме по убыванию</h2>
    <?= $renderer->render($sortedByAmountDesc) ?>
</body>
</html>
