<?php

namespace App\Actions\Users\Delete;

use App\Services\UserService;
use App\Exceptions\AppException;
use App\Helpers\JsonResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Action
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private readonly UserService $userService,
        private readonly Validator $validator,
    ) {}

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $dto = $this->validator->validate($args);

        $this->userService->delete($dto->id);

        return $response->withStatus(204);
    }
}
