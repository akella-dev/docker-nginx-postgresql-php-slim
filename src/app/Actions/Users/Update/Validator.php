<?php

namespace App\Actions\Users\Update;

use App\Exceptions\ValidationException;
use Valitron\Validator as ValitronValidator;

class Validator
{
    public function validate(array $data): RequestDTO
    {
        $v = new ValitronValidator($data);

        $v->rule('required', ['id'])->message('ID обязательно!');
        $v->rule('integer', ['id'])->message('ID должен быть целым числом');
        $v->rule('min', ['id'], 1)->message('ID должен быть положительным числом');

        if (isset($data['name'])) {
            $v->rule('lengthMin', 'name', 2)->message('Имя должно содержать не менее 2 символов');
            $v->rule('lengthMax', 'name', 64)->message('Имя должно содержать не более 64 символов');
        }

        if (isset($data['login'])) {
            $v->rule('lengthMin', 'login', 2)->message('Логин должен содержать не менее 2 символов');
            $v->rule('lengthMax', 'login', 64)->message('Логин должен содержать не более 64 символов');
        }

        if (isset($data['password'])) {
            $v->rule('lengthMin', 'password', 6)->message('Пароль должен содержать не менее 6 символов');
            $v->rule('lengthMax', 'password', 255)->message('Пароль должен содержать не более 255 символов');
        }

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return new RequestDTO(
            id: $data['id'],
            name: $data['name'] ?? null,
            login: $data['login'] ?? null,
            password: $data['password'] ?? null,
        );
    }
}
