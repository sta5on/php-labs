<?php

declare(strict_types=1);

/**
 * Управляет запуском валидаторов формы.
 */
final class FormValidator
{
    /**
     * @param array<string, array<int, ValidatorInterface>> $rules Правила валидации.
     */
    public function __construct(private array $rules)
    {
    }

    /**
     * Валидирует входные данные и возвращает ошибки.
     *
     * @param array<string, string> $input Данные формы.
     * @return array<string, string>
     */
    public function validate(array $input): array
    {
        $errors = [];

        foreach ($this->rules as $field => $validators) {
            $value = $input[$field] ?? '';

            foreach ($validators as $validator) {
                $message = $validator->validate($field, $value, $input);
                if ($message !== null) {
                    $errors[$field] = $message;
                    break;
                }
            }
        }

        return $errors;
    }
}
