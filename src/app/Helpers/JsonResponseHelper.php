<?php

namespace App\Helpers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class JsonResponseHelper
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory
    ) {}

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    public function send(
        mixed $data,
        bool $status = true,
        int $code = 200,
        array $headers = [],
        ResponseInterface|null $response = null,
    ): ResponseInterface {

        if ($response === null) {
            $response = $this->getResponseFactory()->createResponse();
        }

        if (array_key_exists('Content-Type', $headers)) {
            unset($headers['Content-Type']);
        }

        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus($code);

        $response->getBody()->write(json_encode([
            'status' => $status,
            $status ? 'result' : 'error' => $data
        ]));

        return $response;
    }
}
