<?php

namespace App\Exceptions;

class ValidationException extends \RuntimeException
{
    public function __construct(
        public readonly array $errors,
        string $message = 'Валидационная ошибка',
        int $code = 422,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
