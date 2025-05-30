<?php

namespace App\Actions\Users\Update;

class RequestDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly ?string $login,
        public readonly ?string $password,
    ) {}
}
