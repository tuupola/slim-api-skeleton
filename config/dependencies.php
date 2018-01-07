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

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;
use Skeleton\Application\Todo\CreateTodoService;
use Skeleton\Application\Todo\UpdateTodoService;
use Skeleton\Application\Todo\DeleteTodoService;
use Skeleton\Application\Todo\LatestTodoService;
use Skeleton\Application\Todo\ReadTodoService;
use Skeleton\Application\Todo\ReadTodoCollectionService;
use Skeleton\Application\Todo\TransformTodoService;
use Skeleton\Application\Todo\TransformTodoCollectionService;
use Skeleton\Infrastructure\ZendTodoRepository;

$container = $app->getContainer();

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

$container["createTodoService"] = function ($container) {
    return new CreateTodoService($container["todoRepository"]);
};

$container["updateTodoService"] = function ($container) {
    return new UpdateTodoService($container["todoRepository"]);
};

$container["deleteTodoService"] = function ($container) {
    return new DeleteTodoService($container["todoRepository"]);
};

$container["latestTodoService"] = function ($container) {
    return new LatestTodoService($container["todoRepository"]);
};

$container["readTodoService"] = function ($container) {
    return new ReadTodoService($container["todoRepository"]);
};

$container["readTodoCollectionService"] = function ($container) {
    return new ReadTodoCollectionService($container["todoRepository"]);
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
