<?php

namespace App\Actions\Users\Delete;

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

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return new RequestDTO(
            id: $data['id'],
        );
    }
}
