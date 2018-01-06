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

use Skeleton\Application\Response\NotFoundResponse;
use Skeleton\Application\Response\ForbiddenResponse;
use Skeleton\Application\Response\PreconditionFailedResponse;
use Skeleton\Application\Response\PreconditionRequiredResponse;

$app->get("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.list"])) {
        return new ForbiddenResponse("Token not allowed to list todos", 403);
    }

    /* Use ETag and date from Todo with most recent update. */
    $first = $this->latestTodoService->execute();

    /* Add Last-Modified and ETag headers to response when atleast on todo exists. */
    if ($first) {
        $response = $this->cache->withEtag($response, $first->etag());
        $response = $this->cache->withLastModified($response, $first->timestamp());
    }

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response. */
    $todos = $this->readTodoCollectionService->execute();
    $data = $this->transformTodoCollectionService->execute($todos);

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->post("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.create"])) {
        return new ForbiddenResponse("Token not allowed to create todos", 403);
    }

    $data = $request->getParsedBody();
    $todo = $this->createTodoService->execute($data);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

    /* Serialize the response. */
    $data = $this->transformTodoService->execute($todo);

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Content-Location", $data["data"]["links"]["self"])
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->get("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.read"])) {
        return new ForbiddenResponse("Token not allowed to read todos", 403);
    }

    /* Load existing todo using provided uid. */
    try {
        $todo = $this->readTodoService->execute(["uid" => $arguments["uid"]]);
    } catch (RuntimeException $error) {
        return new NotFoundResponse("Todo not found", 404);
    }

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->cache->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response. */
    $data = $this->transformTodoService->execute($todo);

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->patch("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.update"])) {
        return new ForbiddenResponse("Token not allowed to update todos", 403);
    }

    /* Load existing todo using provided uid. */
    try {
        $todo = $this->readTodoService->execute(["uid" => $arguments["uid"]]);
    } catch (RuntimeException $error) {
        return new NotFoundResponse("Todo not found", 404);
    }

    /* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        return new PreconditionRequiredResponse("PATCH request is required to be conditional", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the todo respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $todo->etag(), $todo->timestamp())) {
        return new PreconditionFailedResponse("Todo has already been modified", 412);
    }

    $data = $request->getParsedBody();
    $data["uid"] = $todo->uid(); /* TODO: this is not good... */
    $todo = $this->updateTodoService->execute($data);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->cache->withEtag($response, $todo->etag());
    $response = $this->cache->withLastModified($response, $todo->timestamp());

    $data = $this->transformTodoService->execute($todo);

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->put("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.update"])) {
        return new ForbiddenResponse("Token not allowed to update todos", 403);
    }

    /* Load existing todo using provided uid */
    if (false === $todo = $this->spot->mapper("App\Todo")->first([
        "uid" => $arguments["uid"]
    ])) {
        return new NotFoundResponse("Todo not found", 404);
    };

    /* PUT requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->cache->hasStateValidator($request)) {
        return new PreconditionRequiredResponse("PUT request is required to be conditional", 428);
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the todo respond with 412 Precondition Failed. */
    if (false === $this->cache->hasCurrentState($request, $todo->etag(), $todo->timestamp())) {
        return new PreconditionFailedResponse("Todo has already been modified", 412);
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

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->token->hasScope(["todo.all", "todo.delete"])) {
        return new ForbiddenResponse("Token not allowed to delete todos", 403);
    }

    try {
        $todo = $this->deleteTodoService->execute(["uid" => $arguments["uid"]]);
    } catch (RuntimeException $error) {
        return new NotFoundResponse("Todo not found", 404);
    }

    return $response->withStatus(204);
});
