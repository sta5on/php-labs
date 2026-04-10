<?php

declare(strict_types=1);

/**
 * Управляет формой транзакции: принимает POST, валидирует, сохраняет и готовит данные для вывода.
 */
final class TransactionForm
{
    /**
     * @param FormValidator $validator Валидатор формы.
     * @param TransactionStorage $storage Хранилище транзакций.
     */
    public function __construct(
        private FormValidator $validator,
        private TransactionStorage $storage
    ) {
    }

    /**
     * Обрабатывает POST-запрос и выполняет redirect при необходимости.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }

        $input = $this->normalizeInput($_POST);
        $errors = $this->validator->validate($input);

        if ($errors !== []) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $input;
            header('Location: index.php');
            exit;
        }

        $this->storage->add([
            'id' => $this->storage->nextId(),
            'transaction_date' => $input['transaction_date'],
            'amount' => round((float) $input['amount'], 2),
            'merchant' => $input['merchant'],
            'category' => $input['category'],
            'type' => $input['type'],
            'description' => $input['description'],
            'is_recurring' => $input['is_recurring'] === '1',
            'created_at' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]);

        $_SESSION['flash_success'] = 'Транзакция успешно сохранена.';
        header('Location: index.php');
        exit;
    }

    /**
     * Возвращает старые значения формы из сессии.
     *
     * @return array<string, string>
     */
    public function pullOldInput(): array
    {
        $oldInput = is_array($_SESSION['old_input'] ?? null) ? $_SESSION['old_input'] : [];
        unset($_SESSION['old_input']);

        return $oldInput;
    }

    /**
     * Возвращает ошибки формы из сессии.
     *
     * @return array<string, string>
     */
    public function pullErrors(): array
    {
        $errors = is_array($_SESSION['form_errors'] ?? null) ? $_SESSION['form_errors'] : [];
        unset($_SESSION['form_errors']);

        return $errors;
    }

    /**
     * Возвращает flash-сообщение из сессии.
     *
     * @return string|null
     */
    public function pullFlashSuccess(): ?string
    {
        $message = is_string($_SESSION['flash_success'] ?? null) ? $_SESSION['flash_success'] : null;
        unset($_SESSION['flash_success']);

        return $message;
    }

    /**
     * Возвращает отсортированный список транзакций.
     *
     * @param string $sortField Поле сортировки.
     * @param string $direction Направление сортировки.
     * @return array<int, array<string, mixed>>
     */
    public function sortedTransactions(string $sortField, string $direction): array
    {
        $transactions = $this->storage->all();

        usort(
            $transactions,
            static function (array $left, array $right) use ($sortField, $direction): int {
                $comparison = $sortField === 'amount'
                    ? ((float) ($left[$sortField] ?? 0)) <=> ((float) ($right[$sortField] ?? 0))
                    : strcmp((string) ($left[$sortField] ?? ''), (string) ($right[$sortField] ?? ''));

                return $direction === 'desc' ? -$comparison : $comparison;
            }
        );

        return $transactions;
    }

    /**
     * Возвращает нормализованные данные формы.
     *
     * @param array<string, mixed> $source Сырые данные формы.
     * @return array<string, string>
     */
    private function normalizeInput(array $source): array
    {
        return [
            'transaction_date' => trim((string) ($source['transaction_date'] ?? '')),
            'amount' => trim((string) ($source['amount'] ?? '')),
            'merchant' => trim((string) ($source['merchant'] ?? '')),
            'category' => trim((string) ($source['category'] ?? '')),
            'type' => trim((string) ($source['type'] ?? '')),
            'description' => trim((string) ($source['description'] ?? '')),
            'is_recurring' => isset($source['is_recurring']) ? '1' : '0',
        ];
    }
}
