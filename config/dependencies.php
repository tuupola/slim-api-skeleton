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

$container = $app->getContainer();


use Skeleton\Application\TodoService;
use Skeleton\Infrastructure\ZendTodoRepository;

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

$container["todoService"] = function ($container) {

    return new TodoService($container["todoRepository"]);
};


use Spot\Config;
use Spot\Locator;
use Tuupola\DBAL\Logging\Psr3Logger;

$container["spot"] = function ($container) {

    $config = new Config();
    $mysql = $config->addConnection("mysql", [
        "dbname" => getenv("DB_NAME"),
        "user" => getenv("DB_USER"),
        "password" => getenv("DB_PASSWORD"),
        "host" => getenv("DB_HOST"),
        "driver" => "pdo_mysql",
        "charset" => "utf8"
    ]);

    $spot = new Locator($config);

    $logger = new Psr3Logger($container["logger"]);
    $mysql->getConfiguration()->setSQLLogger($logger);

    return $spot;
};

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;

$container = $app->getContainer();

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
