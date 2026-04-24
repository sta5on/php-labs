<?php

declare(strict_types=1);

/**
 * Отвечает за сохранение и чтение транзакций из JSON-файла.
 */
final class TransactionStorage
{
    /**
     * @param string $filePath Путь к JSON-файлу.
     */
    public function __construct(private string $filePath)
    {
    }

    /**
     * Возвращает все транзакции.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        if (!is_file($this->filePath)) {
            return [];
        }

        $json = file_get_contents($this->filePath);
        if ($json === false || trim($json) === '') {
            return [];
        }

        $transactions = json_decode($json, true);
        return is_array($transactions) ? array_values(array_filter($transactions, 'is_array')) : [];
    }

    /**
     * Сохраняет весь список транзакций.
     *
     * @param array<int, array<string, mixed>> $transactions Список транзакций.
     * @return void
     */
    public function saveAll(array $transactions): void
    {
        $json = json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json !== false) {
            file_put_contents($this->filePath, $json . PHP_EOL, LOCK_EX);
        }
    }

    /**
     * Добавляет одну транзакцию в файл.
     *
     * @param array<string, mixed> $transaction Новая транзакция.
     * @return void
     */
    public function add(array $transaction): void
    {
        $transactions = $this->all();
        $transactions[] = $transaction;
        $this->saveAll($transactions);
    }

    /**
     * Возвращает следующий идентификатор.
     *
     * @return int
     */
    public function nextId(): int
    {
        $transactions = $this->all();
        if ($transactions === []) {
            return 1;
        }

        $ids = array_map(static fn(array $item): int => (int) ($item['id'] ?? 0), $transactions);
        return max($ids) + 1;
    }
}
