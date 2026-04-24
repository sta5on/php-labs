<?php if (($flashSuccess ?? null) !== null): ?>
    <div class="message success"><?= e($flashSuccess) ?></div>
<?php endif; ?>

<?php if (($errors ?? []) !== []): ?>
    <div class="message error">Форма содержит ошибки. Исправьте поля и отправьте данные снова.</div>
<?php endif; ?>

<section class="section">
    <h2>Добавить транзакцию</h2>
    <?= renderPhpTemplate(__DIR__ . '/form.php', [
        'formAction' => $formAction ?? 'index.php',
        'oldInput' => $oldInput ?? [],
        'errors' => $errors ?? [],
        'categories' => $categories ?? [],
        'types' => $types ?? [],
    ]) ?>
</section>

<section class="section">
    <h2>Список транзакций</h2>
    <?= renderPhpTemplate(__DIR__ . '/list.php', [
        'transactions' => $transactions ?? [],
        'sortLinks' => $sortLinks ?? [],
    ]) ?>
</section>
