<?php

declare(strict_types=1);

/**
 * Проверяет дату в формате YYYY-MM-DD.
 */
final class DateValidator implements ValidatorInterface
{
    /**
     * @param string $message Сообщение об ошибке.
     */
    public function __construct(private string $message)
    {
    }

    /**
     * @param string $field Имя поля.
     * @param mixed $value Значение поля.
     * @param array<string, string> $input Все данные формы.
     * @return string|null
     */
    public function validate(string $field, mixed $value, array $input): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        $hasErrors = is_array($errors)
            && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);

        if ($date === false || $hasErrors || $date->format('Y-m-d') !== $value) {
            return $this->message;
        }

        return null;
    }
}
