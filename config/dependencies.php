<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;

use Skeleton\Application\Todo\CreateTodoCommand;
use Skeleton\Application\Todo\CreateTodoHandler;
use Skeleton\Application\Todo\UpdateTodoCommand;
use Skeleton\Application\Todo\UpdateTodoHandler;
use Skeleton\Application\Todo\DeleteTodoCommand;
use Skeleton\Application\Todo\DeleteTodoHandler;
use Skeleton\Application\Todo\LatestTodoQuery;
use Skeleton\Application\Todo\LatestTodoHandler;
use Skeleton\Application\Todo\ReadTodoQuery;
use Skeleton\Application\Todo\ReadTodoHandler;
use Skeleton\Application\Todo\ReadTodoCollectionQuery;
use Skeleton\Application\Todo\ReadTodoCollectionHandler;
use Skeleton\Application\Todo\TransformTodoHandler;
use Skeleton\Application\Todo\TransformTodoCollectionHandler;
use Skeleton\Infrastructure\ZendTodoRepository;

$container = $app->getContainer();

$container["commandBus"] = function ($container) {
    $inflector = new HandleInflector();

    $locator = new InMemoryLocator();
    $locator->addHandler(
        new CreateTodoHandler($container["todoRepository"]),
        CreateTodoCommand::class
    );
    $locator->addHandler(
        new ReadTodoHandler($container["todoRepository"]),
        ReadTodoCommand::class
    );
    $locator->addHandler(
        new ReadTodoCollectionHandler($container["todoRepository"]),
        ReadTodoCollectionCommand::class
    );
    $locator->addHandler(
        new LatestTodoHandler($container["todoRepository"]),
        LatestTodoQuery::class
    );
    $locator->addHandler(
        new DeleteTodoHandler($container["todoRepository"]),
        DeleteTodoCommand::class
    );
    $locator->addHandler(
        new UpdateTodoHandler($container["todoRepository"]),
        UpdateTodoCommand::class
    );

    $nameExtractor = new ClassNameExtractor();

    $commandHandlerMiddleware = new CommandHandlerMiddleware(
        $nameExtractor,
        $locator,
        $inflector
    );

    return new CommandBus([$commandHandlerMiddleware]);
};

$container["todoRepository"] = function ($container) {

    return new ZendTodoRepository([
        "driver" => "Mysqli",
        "database" => getenv("DB_NAME"),
        "username" => getenv("DB_USER"),
        "password" => getenv("DB_PASSWORD"),
        "hostname" => getenv("DB_HOST"),
        "charset" => "utf8",
        // "driver_options" => [
        //     PDO::ATTR_STRINGIFY_FETCHES => false,
        //     PDO::ATTR_EMULATE_PREPARES => false
        // ],
    ]);
};

// $container["createTodoHandler"] = function ($container) {
//     return new CreateTodoHandler($container["todoRepository"]);
// };

$container["updateTodoHandler"] = function ($container) {
    return new UpdateTodoHandler($container["todoRepository"]);
};

$container["deleteTodoHandler"] = function ($container) {
    return new DeleteTodoHandler($container["todoRepository"]);
};

$container["latestTodoHandler"] = function ($container) {
    return new LatestTodoHandler($container["todoRepository"]);
};

$container["readTodoHandler"] = function ($container) {
    return new ReadTodoHandler($container["todoRepository"]);
};

$container["readTodoCollectionHandler"] = function ($container) {
    return new ReadTodoCollectionHandler($container["todoRepository"]);
};

$container["transformTodoHandler"] = function ($container) {
    return new TransformTodoHandler;
};

$container["transformTodoCollectionHandler"] = function ($container) {
    return new TransformTodoCollectionHandler;
};

$container["logger"] = function ($container) {
    $logger = new Logger("slim");

    $formatter = new LineFormatter(
        "[%datetime%] [%level_name%]: %message% %context%\n",
        null,
        true,
        true
    );

    /* Log to timestamped files */
    $rotating = new RotatingFileHandler(__DIR__ . "/../logs/slim.log", 0, Logger::DEBUG);
    $rotating->setFormatter($formatter);
    $logger->pushHandler($rotating);

    return $logger;
};
