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

use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Serializer\DataArraySerializer;

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
use Skeleton\Application\Todo\ReplaceTodoCommand;
use Skeleton\Application\Todo\ReplaceTodoHandler;
use Skeleton\Application\Todo\DeleteTodoCommand;
use Skeleton\Application\Todo\DeleteTodoHandler;
use Skeleton\Application\Todo\LatestTodoQuery;
use Skeleton\Application\Todo\LatestTodoHandler;
use Skeleton\Application\Todo\ReadTodoQuery;
use Skeleton\Application\Todo\ReadTodoHandler;
use Skeleton\Application\Todo\ReadTodoCollectionQuery;
use Skeleton\Application\Todo\ReadTodoCollectionHandler;
use Skeleton\Application\Todo\TransformTodoService;
use Skeleton\Application\Todo\TransformTodoCollectionService;
use Skeleton\Infrastructure\ZendTodoRepository;
use Skeleton\Domain\Todo;

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
        ReadTodoQuery::class
    );
    $locator->addHandler(
        new ReadTodoCollectionHandler($container["todoRepository"]),
        ReadTodoCollectionQuery::class
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
    $locator->addHandler(
        new ReplaceTodoHandler($container["todoRepository"]),
        ReplaceTodoCommand::class
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
    ]);
};

$container["transformTodoService"] = function ($container) {
    return new TransformTodoService;
};

$container["transformTodoCollectionService"] = function ($container) {
    return new TransformTodoCollectionService;
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
