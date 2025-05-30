<?php

namespace App\Actions\Auth\Get;

use App\Builders\UserResponseBuilder;
use App\Exceptions\AppException;
use App\Helpers\JsonResponseHelper;
use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Action
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private readonly AuthService $auth,
        private readonly UserResponseBuilder $userResponseBuilder,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $user = $this->auth->user();

        if (!$user) {
            throw new AppException('Unauthorized', 401);
        }

        return $this->json->send($this->userResponseBuilder->build($user));
    }
}
