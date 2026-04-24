<?php

declare(strict_types=1);

/**
 * Проверяет обязательность поля.
 */
final class RequiredValidator implements ValidatorInterface
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
        return trim((string) $value) === '' ? $this->message : null;
    }
}
