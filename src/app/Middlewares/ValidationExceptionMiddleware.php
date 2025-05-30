<?php

namespace App\Middlewares;

use App\Config;
use App\Exceptions\ValidationException;
use App\Helpers\JsonResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly JsonResponseHelper $json,
        private Config $config
    ) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {

            return $this->json->send($e->errors, false, $e->getCode());
        }
    }
}
