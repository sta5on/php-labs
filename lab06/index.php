<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Validation/ValidatorInterface.php';
require_once __DIR__ . '/src/Validation/RequiredValidator.php';
require_once __DIR__ . '/src/Validation/StringLengthValidator.php';
require_once __DIR__ . '/src/Validation/DateValidator.php';
require_once __DIR__ . '/src/Validation/NumericRangeValidator.php';
require_once __DIR__ . '/src/Validation/InArrayValidator.php';
require_once __DIR__ . '/src/Validation/FormValidator.php';
require_once __DIR__ . '/src/Storage/TransactionStorage.php';
require_once __DIR__ . '/src/Form/TransactionForm.php';

session_start();

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

$categories = [
    'food' => 'Food',
    'transport' => 'Transport',
    'utilities' => 'Utilities',
    'salary' => 'Salary',
    'other' => 'Other',
];

$types = [
    'income' => 'Income',
    'expense' => 'Expense',
];

$validator = new FormValidator([
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

$storage = new TransactionStorage(__DIR__ . '/data/transactions.json');
$form = new TransactionForm($validator, $storage);
$form->handleRequest();

$errors = $form->pullErrors();
$oldInput = $form->pullOldInput();
$flashSuccess = $form->pullFlashSuccess();

$sortableFields = ['transaction_date', 'amount', 'category', 'created_at'];
$sortField = (string) ($_GET['sort'] ?? 'transaction_date');
$sortDirection = (string) ($_GET['direction'] ?? 'asc');

if (!in_array($sortField, $sortableFields, true)) {
    $sortField = 'transaction_date';
}

if (!in_array($sortDirection, ['asc', 'desc'], true)) {
    $sortDirection = 'asc';
}

$transactions = $form->sortedTransactions($sortField, $sortDirection);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 06 | Transactions</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; color: #222; }
        .container { max-width: 900px; margin: 0 auto; padding: 24px 16px 40px; }
        .section { background: #fff; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; }
        .field { display: grid; gap: 6px; margin-bottom: 10px; }
        input, select, textarea, button { font: inherit; }
        input[type="text"], input[type="date"], input[type="number"], select, textarea {
            width: 100%; padding: 8px; border: 1px solid #bbb; background: #fff;
        }
        button { width: fit-content; padding: 8px 14px; border: 1px solid #999; background: #f0f0f0; cursor: pointer; }
        .inline-option { display: inline-flex; align-items: center; gap: 6px; margin-right: 14px; }
        .message { padding: 10px 12px; margin-bottom: 16px; border: 1px solid #ccc; background: #fff; }
        .message.success { border-color: #5e9b6b; }
        .message.error, .required, .error-text { color: #b00020; }
        .sort-links { margin-bottom: 12px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        @media (max-width: 640px) { .inline-option { display: flex; margin-bottom: 8px; } }
    </style>
</head>
<body>
<main class="container">
    <h1>Банковские транзакции</h1>
    <p>Простая учебная форма с ООП-структурой, интерфейсом валидаторов и сохранением данных в JSON</p>

    <?php if ($flashSuccess !== null): ?>
        <div class="message success"><?= e($flashSuccess) ?></div>
    <?php endif; ?>

    <?php if ($errors !== []): ?>
        <div class="message error">Форма содержит ошибки. Исправьте поля и отправьте данные снова</div>
    <?php endif; ?>

    <section class="section">
        <h2>Добавить транзакцию</h2>
        <form action="index.php" method="post" novalidate>
            <div class="field">
                <label for="transaction_date">Дата транзакции <span class="required">*</span></label>
                <input id="transaction_date" name="transaction_date" type="date" value="<?= e($oldInput['transaction_date'] ?? '') ?>" required>
                <?php if (isset($errors['transaction_date'])): ?>
                    <small class="error-text"><?= e($errors['transaction_date']) ?></small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="amount">Сумма <span class="required">*</span></label>
                <input id="amount" name="amount" type="number" step="0.01" min="0.01" max="1000000" value="<?= e($oldInput['amount'] ?? '') ?>" required>
                <?php if (isset($errors['amount'])): ?>
                    <small class="error-text"><?= e($errors['amount']) ?></small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="merchant">Контрагент <span class="required">*</span></label>
                <input id="merchant" name="merchant" type="text" minlength="2" maxlength="100" value="<?= e($oldInput['merchant'] ?? '') ?>" required>
                <?php if (isset($errors['merchant'])): ?>
                    <small class="error-text"><?= e($errors['merchant']) ?></small>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="category">Категория <span class="required">*</span></label>
                <select id="category" name="category" required>
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= ($oldInput['category'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category'])): ?>
                    <small class="error-text"><?= e($errors['category']) ?></small>
                <?php endif; ?>
            </div>

            <fieldset class="field">
                <legend>Тип транзакции <span class="required">*</span></legend>
                <?php foreach ($types as $value => $label): ?>
                    <label class="inline-option">
                        <input type="radio" name="type" value="<?= e($value) ?>" <?= ($oldInput['type'] ?? '') === $value ? 'checked' : '' ?> required>
                        <?= e($label) ?>
                    </label>
                <?php endforeach; ?>
                <?php if (isset($errors['type'])): ?>
                    <small class="error-text"><?= e($errors['type']) ?></small>
                <?php endif; ?>
            </fieldset>

            <div class="field">
                <label for="description">Описание <span class="required">*</span></label>
                <textarea id="description" name="description" rows="5" minlength="10" maxlength="500" required><?= e($oldInput['description'] ?? '') ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <small class="error-text"><?= e($errors['description']) ?></small>
                <?php endif; ?>
            </div>

            <label class="inline-option">
                <input type="checkbox" name="is_recurring" value="1" <?= ($oldInput['is_recurring'] ?? '') === '1' ? 'checked' : '' ?>>
                Повторяющаяся транзакция
            </label>

            <p><button type="submit">Сохранить</button></p>
        </form>
    </section>

    <section class="section">
        <h2>Список транзакций</h2>
        <p class="sort-links">
            Сортировка:
            <a href="<?= e(sortUrl('transaction_date', $sortField, $sortDirection)) ?>">дата</a>,
            <a href="<?= e(sortUrl('amount', $sortField, $sortDirection)) ?>">сумма</a>,
            <a href="<?= e(sortUrl('category', $sortField, $sortDirection)) ?>">категория</a>,
            <a href="<?= e(sortUrl('created_at', $sortField, $sortDirection)) ?>">добавлено</a>
        </p>

        <?php if ($transactions === []): ?>
            <p>Транзакции пока не добавлены.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th><a href="<?= e(sortUrl('transaction_date', $sortField, $sortDirection)) ?>">Дата</a></th>
                        <th><a href="<?= e(sortUrl('amount', $sortField, $sortDirection)) ?>">Сумма</a></th>
                        <th>Контрагент</th>
                        <th><a href="<?= e(sortUrl('category', $sortField, $sortDirection)) ?>">Категория</a></th>
                        <th>Тип</th>
                        <th>Описание</th>
                        <th>Recurring</th>
                        <th><a href="<?= e(sortUrl('created_at', $sortField, $sortDirection)) ?>">Создано</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= e((string) ($transaction['id'] ?? '')) ?></td>
                            <td><?= e((string) ($transaction['transaction_date'] ?? '')) ?></td>
                            <td><?= e(number_format((float) ($transaction['amount'] ?? 0), 2, '.', ' ')) ?></td>
                            <td><?= e((string) ($transaction['merchant'] ?? '')) ?></td>
                            <td><?= e($categories[(string) ($transaction['category'] ?? '')] ?? (string) ($transaction['category'] ?? '')) ?></td>
                            <td><?= e($types[(string) ($transaction['type'] ?? '')] ?? (string) ($transaction['type'] ?? '')) ?></td>
                            <td><?= e((string) ($transaction['description'] ?? '')) ?></td>
                            <td><?= !empty($transaction['is_recurring']) ? 'Yes' : 'No' ?></td>
                            <td><?= e((string) ($transaction['created_at'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
