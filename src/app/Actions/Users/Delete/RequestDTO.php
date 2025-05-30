<?php

namespace App\Actions\Users\Delete;

class RequestDTO
{
    public function __construct(
        public readonly int $id,
    ) {}
}
