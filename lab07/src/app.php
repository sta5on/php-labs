<?php

declare(strict_types=1);

require_once __DIR__ . '/Validation/ValidatorInterface.php';
require_once __DIR__ . '/Validation/RequiredValidator.php';
require_once __DIR__ . '/Validation/StringLengthValidator.php';
require_once __DIR__ . '/Validation/DateValidator.php';
require_once __DIR__ . '/Validation/NumericRangeValidator.php';
require_once __DIR__ . '/Validation/InArrayValidator.php';
require_once __DIR__ . '/Validation/FormValidator.php';
require_once __DIR__ . '/Storage/TransactionStorage.php';
require_once __DIR__ . '/Form/TransactionForm.php';

/**
 * Экранирует значение для безопасного вывода в HTML.
 *
 * @param string|int|float|null $value Выводимое значение.
 * @return string
 */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Рендерит PHP-шаблон и возвращает HTML строкой.
 *
 * @param string $templatePath Абсолютный путь к шаблону.
 * @param array<string, mixed> $params Данные шаблона.
 * @return string
 */
function renderPhpTemplate(string $templatePath, array $params = []): string
{
    extract($params, EXTR_SKIP);

    ob_start();
    require $templatePath;

    return (string) ob_get_clean();
}

/**
 * Формирует ссылку для сортировки таблицы.
 *
 * @param string $field Поле сортировки.
 * @param string $currentField Текущее поле сортировки.
 * @param string $currentDirection Текущее направление сортировки.
 * @return string
 */
function sortUrl(string $field, string $currentField, string $currentDirection): string
{
    $nextDirection = ($field === $currentField && $currentDirection === 'asc') ? 'desc' : 'asc';

    return '?sort=' . urlencode($field) . '&direction=' . urlencode($nextDirection);
}

/**
 * Форматирует сумму в читабельный денежный вид.
 *
 * @param int|float|string $amount Сумма.
 * @param string $currency Валюта.
 * @return string
 */
function formatCurrency(int|float|string $amount, string $currency = 'MDL'): string
{
    $formatted = number_format((float) $amount, 2, '.', ' ');

    return $currency === '' ? $formatted : $formatted . ' ' . $currency;
}

/**
 * Возвращает список категорий транзакций.
 *
 * @return array<string, string>
 */
function transactionCategories(): array
{
    return [
        'food' => 'Food',
        'transport' => 'Transport',
        'utilities' => 'Utilities',
        'salary' => 'Salary',
        'other' => 'Other',
    ];
}

/**
 * Возвращает список типов транзакций.
 *
 * @return array<string, string>
 */
function transactionTypes(): array
{
    return [
        'income' => 'Income',
        'expense' => 'Expense',
    ];
}

/**
 * Создает валидатор формы транзакции.
 *
 * @param array<string, string> $categories Список категорий.
 * @param array<string, string> $types Список типов.
 * @return FormValidator
 */
function createTransactionValidator(array $categories, array $types): FormValidator
{
    return new FormValidator([
        'transaction_date' => [
            new RequiredValidator('Укажите дату транзакции.'),
            new DateValidator('Дата должна быть в формате YYYY-MM-DD.'),
        ],
        'amount' => [
            new RequiredValidator('Укажите сумму транзакции.'),
            new NumericRangeValidator(0.01, 1000000.0, 'Сумма должна быть числом от 0.01 до 1000000.'),
        ],
        'merchant' => [
            new RequiredValidator('Укажите название контрагента.'),
            new StringLengthValidator(2, 100, 'Название контрагента должно содержать от 2 до 100 символов.'),
        ],
        'category' => [
            new RequiredValidator('Выберите категорию.'),
            new InArrayValidator(array_keys($categories), 'Выбрана недопустимая категория.'),
        ],
        'type' => [
            new RequiredValidator('Выберите тип транзакции.'),
            new InArrayValidator(array_keys($types), 'Выбран недопустимый тип транзакции.'),
        ],
        'description' => [
            new RequiredValidator('Заполните описание транзакции.'),
            new StringLengthValidator(10, 500, 'Описание должно содержать от 10 до 500 символов.'),
        ],
    ]);
}

/**
 * Возвращает форму с подготовленными зависимостями.
 *
 * @param string $dataFilePath Путь к JSON-файлу.
 * @return TransactionForm
 */
function createTransactionForm(string $dataFilePath): TransactionForm
{
    $categories = transactionCategories();
    $types = transactionTypes();
    $validator = createTransactionValidator($categories, $types);
    $storage = new TransactionStorage($dataFilePath);

    return new TransactionForm($validator, $storage);
}

/**
 * Возвращает параметры сортировки.
 *
 * @return array{0: string, 1: string}
 */
function resolveSortState(): array
{
    $sortableFields = ['transaction_date', 'amount', 'category', 'created_at'];
    $sortField = (string) ($_GET['sort'] ?? 'transaction_date');
    $sortDirection = (string) ($_GET['direction'] ?? 'asc');

    if (!in_array($sortField, $sortableFields, true)) {
        $sortField = 'transaction_date';
    }

    if (!in_array($sortDirection, ['asc', 'desc'], true)) {
        $sortDirection = 'asc';
    }

    return [$sortField, $sortDirection];
}

/**
 * Возвращает ссылки сортировки для всех колонок.
 *
 * @param string $sortField Текущее поле сортировки.
 * @param string $sortDirection Текущее направление сортировки.
 * @return array<string, string>
 */
function buildSortLinks(string $sortField, string $sortDirection): array
{
    return [
        'transaction_date' => sortUrl('transaction_date', $sortField, $sortDirection),
        'amount' => sortUrl('amount', $sortField, $sortDirection),
        'category' => sortUrl('category', $sortField, $sortDirection),
        'created_at' => sortUrl('created_at', $sortField, $sortDirection),
    ];
}

/**
 * Подготавливает список транзакций к отображению.
 *
 * @param array<int, array<string, mixed>> $transactions Сырые транзакции.
 * @param array<string, string> $categories Список категорий.
 * @param array<string, string> $types Список типов.
 * @return array<int, array<string, mixed>>
 */
function hydrateTransactionsForView(array $transactions, array $categories, array $types): array
{
    return array_map(
        static function (array $transaction) use ($categories, $types): array {
            $categoryKey = (string) ($transaction['category'] ?? '');
            $typeKey = (string) ($transaction['type'] ?? '');

            $transaction['category_label'] = $categories[$categoryKey] ?? $categoryKey;
            $transaction['type_label'] = $types[$typeKey] ?? $typeKey;
            $transaction['is_recurring_label'] = !empty($transaction['is_recurring']) ? 'Yes' : 'No';

            return $transaction;
        },
        $transactions
    );
}

/**
 * Готовит все данные страницы для рендера.
 *
 * @param string $dataFilePath Путь к JSON-файлу.
 * @param string $redirectPath Файл для PRG-редиректа.
 * @return array<string, mixed>
 */
function buildTransactionPageContext(string $dataFilePath, string $redirectPath): array
{
    $categories = transactionCategories();
    $types = transactionTypes();
    $form = createTransactionForm($dataFilePath);
    $form->handleRequest($redirectPath);

    $errors = $form->pullErrors();
    $oldInput = $form->pullOldInput();
    $flashSuccess = $form->pullFlashSuccess();

    [$sortField, $sortDirection] = resolveSortState();
    $sortLinks = buildSortLinks($sortField, $sortDirection);
    $transactions = hydrateTransactionsForView(
        $form->sortedTransactions($sortField, $sortDirection),
        $categories,
        $types
    );

    return [
        'categories' => $categories,
        'types' => $types,
        'errors' => $errors,
        'oldInput' => $oldInput,
        'flashSuccess' => $flashSuccess,
        'transactions' => $transactions,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
        'sortLinks' => $sortLinks,
    ];
}
