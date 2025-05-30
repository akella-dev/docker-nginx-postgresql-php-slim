<?php

namespace App\Actions\Users\Create;

use App\Exceptions\ValidationException;
use Valitron\Validator as ValitronValidator;

class Validator
{
    public function validate(array $data): RequestDTO
    {
        $v = new ValitronValidator($data);

        $v->rule('required', ['name', 'login', 'password'])->message('Данное поле обязательно!');
        $v->rule('lengthMin', ['name', 'login'], 2)->message('Длина должна быть не менее 2 символов');
        $v->rule('lengthMax', ['name', 'login'], 64)->message('Длина должна быть не более 64 символов');
        $v->rule('lengthMin', 'password', 6)->message('Пароль должен содержать не менее 6 символов');
        $v->rule('lengthMax', 'password', 255)->message('Пароль должен содержать не более 255 символов');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return new RequestDTO(
            name: $data['name'],
            login: $data['login'],
            password: $data['password'],
        );
    }
}
