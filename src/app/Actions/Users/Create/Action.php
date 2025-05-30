<?php

namespace App\Actions\Users\Create;

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

    public function __invoke(Request $request, Response $response): Response
    {
        $reqDTO = $this->validator->validate($request->getParsedBody() ?? []);

        $user = $this->userService->create(
            $reqDTO->name,
            $reqDTO->login,
            $reqDTO->password
        );

        return $this->json->send($this->userResponseBuilder->build($user), true, 201);
    }
}
