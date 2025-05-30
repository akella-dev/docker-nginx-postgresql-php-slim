<?php

namespace App\Actions\Users\Get;

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
        private readonly UserResponseBuilder $userResponseBuilder

    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $users = $this->userService->getAll();

        $result = array_map(function ($user) {
            return $this->userResponseBuilder->build($user);
        }, $users);

        return $this->json->send($result);
    }
}
