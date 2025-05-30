<?php

use App\Config;
use App\Enums\SameSite;
use App\Session;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return [
    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        /** @var Config $config */
        $config = $container->get(Config::class);
        $app->setBasePath($config->get('slim.base_path'));

        (require PATH_CONFIG_FOLDER . '/router.php')($app);
        (require PATH_CONFIG_FOLDER . '/middleware.php')($app);

        return $app;
    },
    Config::class => function () {
        $config = new Config();
        $config->load(require PATH_CONFIG_FOLDER . '/app.php');

        return $config;
    },
    Session::class => fn(Config $config) => new Session(
        $config->get('session.name', ''),
        $config->get('session.flash_name', 'flash'),
        $config->get('session.secure', true),
        $config->get('session.httponly', true),
        SameSite::from($config->get('session.samesite', 'lax'))
    ),
    LoggerInterface::class => function (Config $config) {
        // Создаем логгер
        $logger = new Logger($config->get('logger.name'));

        // Создаем обработчик (StreamHandler)
        $handler = new RotatingFileHandler($config->get('logger.path'), $config->get('logger.max_files'));

        // Настраиваем форматтер
        $dateFormat = "d.m.Y H:i:s"; // Формат даты: 26.12.2025 13:56:59
        $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"; // Формат вывода
        $formatter = new LineFormatter($outputFormat, $dateFormat);

        // Применяем форматтер к обработчику
        $handler->setFormatter($formatter);

        // Добавляем обработчик в логгер
        $logger->pushHandler($handler);

        return $logger;
    },
    EntityManagerInterface::class => function (Config $config) {

        $ormConfig = ORMSetup::createAttributeMetadataConfiguration($config->get('doctrine.entity_dir'), $config->get('doctrine.dev_mode'));
        $conn = DriverManager::getConnection($config->get('doctrine.connection'), $ormConfig);

        return new EntityManager($conn, $ormConfig);
    },
    ResponseFactoryInterface::class         => fn(App $app) => $app->getResponseFactory(),
];
