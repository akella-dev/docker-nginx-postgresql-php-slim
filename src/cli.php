<?php

use App\Config;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/** @var ContainerInterface $container  */
$container = require 'boot.php';

$config = $container->get(Config::class);
$entityManager = $container->get(EntityManagerInterface::class);
$dependencyFactory = DependencyFactory::fromEntityManager(
    new PhpFile(PATH_CONFIG_FOLDER . '/migrations.php'),
    new ExistingEntityManager($entityManager)
);

$migrationCommands = require PATH_CONFIG_FOLDER . '/commands/migrations.php';
$customCommands = require PATH_CONFIG_FOLDER . '/commands/commands.php';

$cli = new Application($config->get('app.name'), $config->get('app.version'));

ConsoleRunner::addCommands($cli, new SingleManagerProvider($entityManager));

$cli->addCommands($migrationCommands($dependencyFactory));
$cli->addCommands(array_map(fn($command) => $container->get($command), $customCommands));

$cli->run();
