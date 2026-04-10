<?php

declare(strict_types=1);

/**
 * Проверяет значение по списку допустимых вариантов.
 */
final class InArrayValidator implements ValidatorInterface
{
    /**
     * @param array<int, string> $allowedValues Допустимые значения.
     * @param string $message Сообщение об ошибке.
     */
    public function __construct(
        private array $allowedValues,
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

        return in_array($value, $this->allowedValues, true) ? null : $this->message;
    }
}
