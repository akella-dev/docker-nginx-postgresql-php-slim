<?php

namespace App\Exceptions;

class AppException extends \RuntimeException
{
    public function __construct(
        string $message = 'Серверная ошибка',
        int $code = 500,
        ?\Throwable $previous = null,
        public readonly ?string $logTitle = null,
        public readonly ?array $logDetails = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
