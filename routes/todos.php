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
use Exception\ForbiddenException;
use Exception\PreconditionFailedException;
use Exception\PreconditionRequiredException;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

$app->get("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.list"])) {
        throw new ForbiddenException("Token not allowed to list todos.", 403);
    }

    /* Use ETag and date from Todo with most recent update. */
    $first = $this->spot->mapper("App\Todo")
        ->all()
        ->order(["updated_at" => "DESC"])
        ->first();

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $first->etag());
    $response = $this->cache->withLastModified($response, $first->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    $todos = $this->spot->mapper("App\Todo")
        ->all()
        ->order(["updated_at" => "DESC"]);

    /* Serialize the response data. */
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
    if (false === $this->token->hasScope(["todo.all", "todo.create"])) {
        throw new ForbiddenException("Token not allowed to create todos.", 403);
    }

    $body = $request->getParsedBody();

    $todo = new Todo($body);
    $this->spot->mapper("App\Todo")->save($todo);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

    /* Serialize the response data. */
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

$app->get("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.read"])) {
        throw new ForbiddenException("Token not allowed to list todos.", 403);
    }

    /* Load existing todo using provided uid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uid" => $arguments["uid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response data. */
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer);
    $resource = new Item($todo, new TodoTransformer);
    $data = $fractal->createData($resource)->toArray();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.update"])) {
        throw new ForbiddenException("Token not allowed to update todos.", 403);
    }

    /* Load existing todo using provided uid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uid" => $arguments["uid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    /* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PATCH request is required to be conditional.", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the todo respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $todo->etag(), $todo->timestamp())) {
        throw new PreconditionFailedException("Todo has been modified.", 412);
    }

    $body = $request->getParsedBody();
    $todo->data($body);
    $this->spot->mapper("App\Todo")->save($todo);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

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

$app->put("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.update"])) {
        throw new ForbiddenException("Token not allowed to update todos.", 403);
    }

    /* Load existing todo using provided uid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uid" => $arguments["uid"]
    ])) {
        throw new NotFoundException("Todo not found.", 404);
    };

    /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        throw new PreconditionRequiredException("PUT request is required to be conditional.", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the todo respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $todo->etag(), $todo->timestamp())) {
        throw new PreconditionFailedException("Todo has been modified.", 412);
    }

    $body = $request->getParsedBody();

    /* PUT request assumes full representation. If any of the properties is */
    /* missing set them to default values by clearing the todo object first. */
    $todo->clear();
    $todo->data($body);
    $this->spot->mapper("App\Todo")->save($todo);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

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

$app->delete("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.delete"])) {
        throw new ForbiddenException("Token not allowed to delete todos.", 403);
    }

    /* Load existing todo using provided uid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uid" => $arguments["uid"]
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
