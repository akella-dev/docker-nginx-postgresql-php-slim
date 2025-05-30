<?php

namespace App\Actions\Users\Update;

use App\Builders\UserResponseBuilder;
use App\Helpers\JsonResponseHelper;
use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Action
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private readonly UserService $userService,
        private readonly Validator $validator,
        private readonly UserResponseBuilder $userResponseBuilder
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $dto = $this->validator->validate(array_merge(json_decode($request->getBody(), true) ?? [], $args ?? []));

        $user = $this->userService->update(
            $dto->id,
            $dto->name,
            $dto->login,
            $dto->password
        );

        return $this->json->send($this->userResponseBuilder->build($user), 200);
    }
}
