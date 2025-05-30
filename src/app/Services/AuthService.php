<?php

namespace App\Services;

use App\Entity\User;
use App\Exceptions\AppException;
use App\Session;

class AuthService
{
    private const AUTH_KEY = 'auth_user_id';

    public function __construct(
        private readonly Session $session,
        private readonly UserService $userService
    ) {}

    public function login(string $login, string $password): User
    {
        if ($this->check()) {
            throw new AppException('You are already logged in. To log in again, log out first.', 409);
        }

        $user = $this->userService->getByLogin($login);
        if (!$user || !$this->verifyPassword($user, $password)) {
            throw new AppException('Invalid credentials', 401);
        }

        $this->session->regenerate();
        $this->session->put(self::AUTH_KEY, $user->getId());

        return $user;
    }

    private function verifyPassword(User $user, string $password): bool
    {
        return password_verify($user->getSalt() . $password, $user->getPassword());
    }

    public function logout(): void
    {
        if (!$this->check()) {
            throw new AppException('You cannot log out because the user is not logged in.', 409);
        }

        $this->session->regenerate();
        $this->session->forget(self::AUTH_KEY);
    }

    public function check(): bool
    {
        return $this->session->has(self::AUTH_KEY);
    }

    public function user(): ?User
    {
        if (!$this->check()) {
            return null;
        }

        $userId = $this->session->get(self::AUTH_KEY);
        return $this->userService->find($userId);
    }

    public function id(): ?int
    {
        return $this->session->get(self::AUTH_KEY);
    }
}
