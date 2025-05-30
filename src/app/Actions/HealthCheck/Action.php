<?php

namespace App\Actions\HealthCheck;

use App\Exceptions\AppException;
use App\Helpers\JsonResponseHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Action
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        if (!$this->em->getConnection()->isConnected()) {
            throw new AppException('Not connected to database', 500);
        }

        return $this->json->send('ok', 200);
    }
}
