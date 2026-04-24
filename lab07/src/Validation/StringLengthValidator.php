<?php

declare(strict_types=1);

/**
 * Проверяет длину строки.
 */
final class StringLengthValidator implements ValidatorInterface
{
    /**
     * @param int $min Минимальная длина.
     * @param int $max Максимальная длина.
     * @param string $message Сообщение об ошибке.
     */
    public function __construct(
        private int $min,
        private int $max,
        private string $message
    ) {
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

        $length = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
        return $length < $this->min || $length > $this->max ? $this->message : null;
    }
}
