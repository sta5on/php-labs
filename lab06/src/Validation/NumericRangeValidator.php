<?php

declare(strict_types=1);

/**
 * Проверяет число на попадание в диапазон.
 */
final class NumericRangeValidator implements ValidatorInterface
{
    /**
     * @param float $min Минимальное значение.
     * @param float $max Максимальное значение.
     * @param string $message Сообщение об ошибке.
     */
    public function __construct(
        private float $min,
        private float $max,
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

        if (!is_numeric($value)) {
            return $this->message;
        }

        $number = (float) $value;
        return $number < $this->min || $number > $this->max ? $this->message : null;
    }
}
