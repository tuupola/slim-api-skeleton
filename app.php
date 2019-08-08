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

date_default_timezone_set("UTC");
error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . "/vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use DI\Container;
use Slim\Factory\AppFactory;
use Skeleton\Infrastructure\Slim\Handler\ApiErrorHandler;

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

require __DIR__ . "/config/dependencies.php";
#require __DIR__ . "/config/handlers.php";
require __DIR__ . "/config/middleware.php";

$errorHandler = new ApiErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory()
);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$app->get("/", function ($request, $response, $arguments) {
    print "Here be dragons";
});

require __DIR__ . "/routes/token.php";
require __DIR__ . "/routes/todos.php";

$app->run();
