<form action="<?= e($formAction ?? 'index.php') ?>" method="post" novalidate>
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
            <?php foreach (($categories ?? []) as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= ($oldInput['category'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?>
            <small class="error-text"><?= e($errors['category']) ?></small>
        <?php endif; ?>
    </div>

    <fieldset class="field">
        <legend>Тип транзакции <span class="required">*</span></legend>
        <?php foreach (($types ?? []) as $value => $label): ?>
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
