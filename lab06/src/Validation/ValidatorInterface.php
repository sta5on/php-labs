<?php

declare(strict_types=1);

/**
 * Общий контракт для валидаторов полей формы.
 */
interface ValidatorInterface
{
    /**
     * Проверяет значение поля и возвращает сообщение об ошибке или null.
     *
     * @param string $field Имя поля.
     * @param mixed $value Значение поля.
     * @param array<string, string> $input Все данные формы.
     * @return string|null
     */
    public function validate(string $field, mixed $value, array $input): ?string;
}
