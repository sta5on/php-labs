<p class="sort-links">
    Сортировка:
    <a href="<?= e($sortLinks['transaction_date'] ?? '?sort=transaction_date&direction=asc') ?>">дата</a>,
    <a href="<?= e($sortLinks['amount'] ?? '?sort=amount&direction=asc') ?>">сумма</a>,
    <a href="<?= e($sortLinks['category'] ?? '?sort=category&direction=asc') ?>">категория</a>,
    <a href="<?= e($sortLinks['created_at'] ?? '?sort=created_at&direction=asc') ?>">добавлено</a>
</p>

<?php if (($transactions ?? []) === []): ?>
    <p>Транзакции пока не добавлены.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th><a href="<?= e($sortLinks['transaction_date'] ?? '?sort=transaction_date&direction=asc') ?>">Дата</a></th>
                <th><a href="<?= e($sortLinks['amount'] ?? '?sort=amount&direction=asc') ?>">Сумма</a></th>
                <th>Контрагент</th>
                <th><a href="<?= e($sortLinks['category'] ?? '?sort=category&direction=asc') ?>">Категория</a></th>
                <th>Тип</th>
                <th>Описание</th>
                <th>Recurring</th>
                <th><a href="<?= e($sortLinks['created_at'] ?? '?sort=created_at&direction=asc') ?>">Создано</a></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (($transactions ?? []) as $transaction): ?>
                <tr>
                    <td><?= e((string) ($transaction['id'] ?? '')) ?></td>
                    <td><?= e((string) ($transaction['transaction_date'] ?? '')) ?></td>
                    <td><?= e(formatCurrency((float) ($transaction['amount'] ?? 0), '')) ?></td>
                    <td><?= e((string) ($transaction['merchant'] ?? '')) ?></td>
                    <td><?= e((string) ($transaction['category_label'] ?? '')) ?></td>
                    <td><?= e((string) ($transaction['type_label'] ?? '')) ?></td>
                    <td><?= e((string) ($transaction['description'] ?? '')) ?></td>
                    <td><?= e((string) ($transaction['is_recurring_label'] ?? 'No')) ?></td>
                    <td><?= e((string) ($transaction['created_at'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
