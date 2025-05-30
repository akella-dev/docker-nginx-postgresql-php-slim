<?php

namespace App\Middlewares;

use App\Config;
use App\Exceptions\AppException;
use App\Helpers\JsonResponseHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;

class AppExceptionMiddleware implements MiddlewareInterface
{
    private const HTTP_EXCEPTION_MAP = [
        HttpNotFoundException::class => ['Ресурс не найден', 404],
        HttpBadRequestException::class => ['Неверный запрос', 400],
        HttpUnauthorizedException::class => ['Не авторизовано', 401],
        HttpForbiddenException::class => ['Доступ запрещен', 403],
        HttpInternalServerErrorException::class => ['Внутренняя ошибка сервера', 500],
        HttpMethodNotAllowedException::class => ['Метод не разрешен', 405],
        HttpNotImplementedException::class => ['Не реализовано', 501],
    ];

    public function __construct(
        private readonly JsonResponseHelper $json,
        private Config $config,
        private LoggerInterface $logger,
    ) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        $requestId = $request->getHeaderLine($this->config->get('logger.request_id_header_name'));
        $requestMethod = $request->getMethod();
        $requestPath = $request->getUri()->getPath();

        $this->setPhpErrorHandler(
            requestId: $requestId,
            requestMethod: $requestMethod,
            requestPath: $requestPath,
        );

        try {
            return $handler->handle($request);
        } catch (AppException $e) {
            $this->handleAppException(
                requestId: $requestId,
                requestMethod: $requestMethod,
                requestPath: $requestPath,
                errorCode: $e->getCode(),
                errorFile: $e->getFile(),
                errorFileLine: $e->getLine(),
                logTitle: $e->logTitle,
                logDetails: $e->logDetails
            );

            return $this->json->send($e->getMessage(), false, $e->getCode());
        } catch (\Throwable $th) {

            $response = $this->handleHttpException($th);
            if ($response !== null) {
                return $response;
            }

            $this->handleAppException(
                requestId: $requestId,
                requestMethod: $requestMethod,
                requestPath: $requestPath,
                errorCode: $th->getCode(),
                errorFile: $th->getFile(),
                errorFileLine: $th->getLine(),
                logTitle: $th->getMessage(),
                logDetails: $th->getTrace()
            );

            return $this->json->send('Серверная ошибка, попробуйте выполнить запрос позже.', false, 500);
        } finally {
            restore_error_handler();
        }
    }

    private function handleAppException(
        string $requestId,
        string $requestMethod,
        string $requestPath,
        int $errorCode,
        string $errorFile,
        int $errorFileLine,
        ?string $logTitle,
        ?array $logDetails,
    ): void {
        $errorBody =   [
            'ID запроса' => $requestId,
            'Адрес запроса' => "$requestMethod $requestPath",
            'Код ошибки' => $errorCode,
            'Путь до файла' => $errorFile,
            'Строка ошибки' => $errorFileLine,
            'Заголовок ошибки' => $logTitle,
            'Детальная информация' =>  $logDetails ?? '<пусто>'
        ];

        $logMessage = sprintf(
            "[%s][%s] %s %s",
            $requestId,
            $requestMethod,
            $requestPath,
            json_encode($errorBody, JSON_UNESCAPED_UNICODE),
        );

        $this->logger->error($logMessage, $errorBody);
    }

    private function handleHttpException(\Throwable $th): ?Response
    {
        foreach (self::HTTP_EXCEPTION_MAP as $exceptionClass => [$message, $code]) {
            if ($th instanceof $exceptionClass) {
                return $this->json->send($message, false, $code);
            }
        }

        return null;
    }

    private function setPhpErrorHandler(string $requestId, string $requestMethod, string $requestPath): void
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($requestId, $requestMethod, $requestPath) {
            $this->handleAppException(
                requestId: $requestId,
                requestMethod: $requestMethod,
                requestPath: $requestPath,
                errorCode: 500,
                errorFile: $errfile,
                errorFileLine: $errline,
                logTitle: $errstr,
                logDetails: ['Код ошибки' => $errno]
            );

            return true;
        });
    }
}
