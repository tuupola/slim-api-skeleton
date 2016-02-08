<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

use App\Todo;
use App\TodoTransformer;

use Exception\NotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */

    $todos = $this->spot->mapper("App\Todo")->all();

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Collection($todos, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */

    $body = $request->getParsedBody();

    $todo = new Todo($body);
    $this->spot->mapper("App\Todo")->save($todo);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "New todo created";

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/todos/{uuid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */

    /* Load existing todo using provided uuid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uuid" => $arguments["uuid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/todos/{uuid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */

    /* Load existing todo using provided uuid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uuid" => $arguments["uuid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    $body = $request->getParsedBody();

    $todo->data($body);
    $this->spot->mapper("App\Todo")->save($todo);

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();
    $data["status"] = "ok";
    $data["message"] = "Todo updated";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/todos/{uuid}", function ($request, $response, $arguments) {

    /* Load existing todo using provided uuid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uuid" => $arguments["uuid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    $this->spot->mapper("App\Todo")->delete($todo);

    $data["status"] = "ok";
    $data["message"] = "Todo deleted";

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});
