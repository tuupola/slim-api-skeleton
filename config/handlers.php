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

use Skeleton\Infrastructure\Slim\Handlers\ApiError as ApiErrorHandler;
use Skeleton\Infrastructure\Slim\Handlers\NotFound as NotFoundHandler;

$container = $app->getContainer();

$container["errorHandler"] = function ($container) {
    return new ApiErrorHandler($container["logger"]);
};

$container["phpErrorHandler"] = function ($container) {
    return $container["errorHandler"];
};

$container["notFoundHandler"] = function ($container) {
    return new NotFoundHandler;
};
