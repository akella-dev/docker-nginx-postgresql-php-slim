<?php

use App\Config;
use App\Middlewares\AppExceptionMiddleware;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\StartSessionMiddleware;
use App\Middlewares\ValidationExceptionMiddleware;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();
    $config = $container->get(Config::class);

    $app->add(ValidationExceptionMiddleware::class);
    $app->add(AppExceptionMiddleware::class);
    $app->add(StartSessionMiddleware::class);
    $app->add(CorsMiddleware::class);

    $app->addErrorMiddleware(
        (bool) $config->get('slim.display_error_details'),
        (bool) $config->get('slim.log_errors'),
        (bool) $config->get('slim.log_error_details')
    );
};
