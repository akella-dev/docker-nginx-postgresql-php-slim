<?php

namespace App\Actions\Auth\Login;

class RequestDTO
{
    public function __construct(
        public readonly string $login,
        public readonly string $password,
    ) {}
}
