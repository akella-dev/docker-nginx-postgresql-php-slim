<?php

namespace App\Actions\Users\Create;

class RequestDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $login,
        public readonly string $password,
    ) {}
}
