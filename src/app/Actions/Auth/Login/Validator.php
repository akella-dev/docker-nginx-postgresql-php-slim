<?php

namespace App\Actions\Auth\Login;

use App\Exceptions\ValidationException;
use Valitron\Validator as ValitronValidator;

class Validator
{
    public function validate(array $data): RequestDTO
    {
        $v = new ValitronValidator($data);

        $v->rule('required', ['login', 'password'])->message('Данное поле обязательно!');
        $v->rule('lengthMin', 'login', 2)->message('Длина должна быть не менее 2 символов');
        $v->rule('lengthMax', 'login', 64)->message('Длина должна быть не более 64 символов');
        $v->rule('lengthMin', 'password', 6)->message('Пароль должен быть не менее 6 символов');
        $v->rule('lengthMax', 'password', 128)->message('Пароль должен быть не более 128 символов');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return new RequestDTO(
            login: $data['login'],
            password: $data['password'],
        );
    }
}
