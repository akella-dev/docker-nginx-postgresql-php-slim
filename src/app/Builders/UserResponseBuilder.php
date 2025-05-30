<?php

namespace App\Builders;

use App\Entity\User;

class UserResponseBuilder
{
    public function build(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'role' => $user->getRole()->value,
            'avatar' => $user->getAvatar(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
