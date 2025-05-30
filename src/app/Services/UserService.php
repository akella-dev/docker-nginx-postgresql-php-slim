<?php

namespace App\Services;

use App\Config;
use App\Entity\User;
use App\Enums\UserRole;
use App\Exceptions\AppException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\UploadedFileInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Config $config,
    ) {}

    public function create(string $name, string $login, string $password): User
    {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
        if ($existingUser) {
            throw new AppException('User with this login already exists', 409);
        }

        // Генерируем случайную соль
        $salt = bin2hex(random_bytes(16));

        $user = new User();
        $user->setName($name)
            ->setLogin($login)
            ->setSalt($salt)
            ->setPassword(password_hash($salt . $password, PASSWORD_DEFAULT))
            ->setRole(UserRole::USER);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function update(int $id, ?string $name, ?string $login, ?string $password = null, ?UploadedFileInterface $avatar = null): User
    {
        $user = $this->entityManager->find(User::class, $id);
        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        if ($name) {
            $user->setName($name);
        }

        if ($login) {
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
            if ($existingUser && $existingUser->getId() !== $id) {
                throw new AppException('User with this login already exists', 409);
            }

            $user->setLogin($login);
        }

        if ($password) {
            // Генерируем новую соль при изменении пароля
            $salt = bin2hex(random_bytes(16));
            $user->setSalt($salt);
            $user->setPassword(password_hash($salt . $password, PASSWORD_DEFAULT));
        }

        if ($avatar) {
            $this->uploadAvatar($user, $avatar);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->entityManager->find(User::class, $id);
        if (!$user) {
            throw new AppException('User not found');
        }

        // Удаляем аватарку пользователя, если она существует
        $this->deleteAvatar($user);

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function find(int $id): ?User
    {
        return $this->getById($id);
    }

    public function getById(int $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function getByLogin(string $login): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
    }

    /**
     * Загружает аватарку для пользователя
     * 
     * @param User $user Пользователь
     * @param UploadedFileInterface $avatar Загруженный файл аватарки
     * @throws AppException Если файл не является изображением или превышает допустимый размер
     */
    public function uploadAvatar(User $user, UploadedFileInterface $avatar): void
    {
        $avatarDir = $this->config->get('storage.avatars');
        $this->deleteAvatar($user);

        // Создаем директорию для аватарок, если она не существует
        if (!file_exists($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }

        // Генерируем уникальное имя файла
        $extension = pathinfo($avatar->getClientFilename(), PATHINFO_EXTENSION);;
        $fileName = uniqid('avatar_') . '.' . $extension;
        $filePath = $avatarDir . '/' . $fileName;

        // Сохраняем файл
        $avatar->moveTo($filePath);

        // Обновляем путь к аватарке в БД
        $user->setAvatar($fileName);
    }

    /**
     * Удаляет аватарку пользователя
     * 
     * @param User $user Пользователь
     */
    private function deleteAvatar(User $user): void
    {
        $avatarDir = $this->config->get('storage.avatars');
        $avatarFileName = $user->getAvatar();

        if ($avatarFileName) {
            $avatarPath = $avatarDir . '/' . $avatarFileName;
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
            $user->setAvatar(null);
        }
    }
}
