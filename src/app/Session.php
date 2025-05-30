<?php

namespace App;

use App\Enums\SameSite;
use App\Exceptions\AppException;

class Session
{
 public function __construct(
        public readonly string $name,
        public readonly string $flashName,
        public readonly bool $secure,
        public readonly bool $httpOnly,
        public readonly SameSite $sameSite
    ) {
    }

    public function start(): void
    {
        if ($this->isActive()) {
            throw new AppException(logTitle: 'Session has already been started');
        }

        if (headers_sent($fileName, $line)) {
            throw new AppException(logTitle: 'Headers have already sent by ' . $fileName . ':' . $line);
        }

        session_set_cookie_params(
            [
                'secure'   => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite->value,
            ]
        );

        if (!empty($this->name)) {
            session_name($this->name);
        }

        if (!session_start()) {
            throw new AppException(logTitle: 'Unable to start the session');
        }
    }

    public function save(): void
    {
        session_write_close();
    }

    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function regenerate(): bool
    {
        return session_regenerate_id();
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, array $data): void
    {
        $_SESSION[$this->flashName][$key] = $data;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $data = $_SESSION[$this->flashName][$key] ?? $default;
        unset($_SESSION[$this->flashName][$key]);

        return $data;
    }
}
