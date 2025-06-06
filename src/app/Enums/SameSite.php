<?php

declare(strict_types=1);

namespace App\Enums;

enum SameSite: string
{
    case Lax = 'lax';
    case None = 'none';
    case Strict = 'strict';
}
