<?php

namespace App\Actions\Auth\Logout;

use App\Helpers\JsonResponseHelper;
use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Action
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private readonly AuthService $auth,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $this->auth->logout();

        return $this->json->send('Successful logout');
    }
}
