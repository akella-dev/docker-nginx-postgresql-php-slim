<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy as Proxy;
use App\Middlewares\AuthMiddleware;

return function (App $app) {
    $app->get('/health-check', \App\Actions\HealthCheck\Action::class);

    $app->group('/auth', function (Proxy $group) {
        $group->get('/get', \App\Actions\Auth\Get\Action::class);
        $group->post('/login', \App\Actions\Auth\Login\Action::class);
        $group->post('/logout', \App\Actions\Auth\Logout\Action::class)->add(AuthMiddleware::class);
    });

    $app->group('/users', function (Proxy $group) {
        $group->post('', \App\Actions\Users\Create\Action::class);
        $group->group('', function (Proxy $group) {
            $group->get('', \App\Actions\Users\Get\Action::class);
            $group->put('/{id}', \App\Actions\Users\Update\Action::class);
            $group->delete('/{id}', \App\Actions\Users\Delete\Action::class);
        })->add(AuthMiddleware::class);
    });
};
