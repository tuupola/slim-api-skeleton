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

use Skeleton\Application\Response\{
    NotFoundResponseFactory,
    ForbiddenResponseFactory,
    PreconditionFailedResponseFactory,
    PreconditionRequiredResponseFactory
};

use Skeleton\Application\Todo\{
    CreateTodoCommand,
    ReadTodoQuery,
    DeleteTodoCommand,
    LatestTodoQuery,
    ReadTodoCommand,
    ReplaceTodoCommand,
    UpdateTodoCommand,
    ReadTodoCollectionQuery,
    TodoNotFoundException
};

use Skeleton\Domain\{
    TodoUid
};

$app->get("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->get("token")->hasScope(["todo.all", "todo.list"])) {
        return (new ForbiddenResponseFactory)->create(
            "Token not allowed to list todos",
            403
        );
    }

    /* Add Last-Modified and ETag headers to response when atleast one todo exists. */
    try {
        $query = new LatestTodoQuery;
        $first = $this->get("commandBus")->handle($query);
        $response = $this->get("cache")->withEtag($response, $first->etag());
        $response = $this->get("cache")->withLastModified($response, $first->timestamp());
    } catch (TodoNotFoundException $exception) {
    }

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->get("cache")->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response. */
    $query = new ReadTodoCollectionQuery;
    $todos = $this->get("commandBus")->handle($query);
    $data = $this->get("transformTodoCollectionService")->execute($todos);
    $body = $response->getBody();
    $body->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->withBody($body);
});

$app->post("/todos", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->get("token")->hasScope(["todo.all", "todo.create"])) {
        return (new ForbiddenResponseFactory)->create(
            "Token not allowed to create todos",
            403
        );
    }

    $data = json_decode($request->getBody(), true) ?: [];
    $uid = $this->get("todoRepository")->nextIdentity();

    $command = new CreateTodoCommand(
        $uid,
        $data["title"],
        $data["order"]
    );
    $this->get("commandBus")->handle($command);

    $query = new ReadTodoQuery($uid);
    $todo = $this->get("commandBus")->handle($query);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->get("cache")->withEtag($response, $todo->etag());
    $response = $this->get("cache")->withLastModified($response, $todo->timestamp());

    /* Serialize the response. */
    $data = $this->get("transformTodoService")->execute($todo);

    $body = $response->getBody();
    $body->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Content-Location", $data["data"]["links"]["self"])
        ->withBody($body);
});

$app->get("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->get("token")->hasScope(["todo.all", "todo.read"])) {
        return (new ForbiddenResponseFactory)->create(
            "Token not allowed to read todos",
            403
        );
    }

    $uid = new TodoUid($arguments["uid"]);

    /* Load existing todo using provided uid. */
    try {
        $query = new ReadTodoQuery($uid);
        $todo = $this->get("commandBus")->handle($query);
    } catch (TodoNotFoundException $exception) {
        return (new NotFoundResponseFactory)->create("Todo not found", 404);
    }

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->get("cache")->withEtag($response, $todo->etag());
    $response = $this->get("cache")->withLastModified($response, $todo->timestamp());

    /* If-Modified-Since and If-None-Match request header handling. */
    /* Heads up! Apache removes previously set Last-Modified header */
    /* from 304 Not Modified responses. */
    if ($this->get("cache")->isNotModified($request, $response)) {
        return $response->withStatus(304);
    }

    /* Serialize the response. */
    $data = $this->get("transformTodoService")->execute($todo);
    $body = $response->getBody();
    $body->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->withBody($body);
});

$app->map(["PUT", "PATCH"], "/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->get("token")->hasScope(["todo.all", "todo.update"])) {
        return (new ForbiddenResponseFactory)->create(
            "Token not allowed to update todos",
            403
        );
    }

    $uid = new TodoUid($arguments["uid"]);

    /* Load existing todo using provided uid. */
    try {
        $query = new ReadTodoQuery($uid);
        $todo = $this->get("commandBus")->handle($query);
    } catch (TodoNotFoundException $exception) {
        return (new NotFoundResponseFactory)->create("Todo not found", 404);
    }

    /* PATCH requires If-Unmodified-Since or If-Match request header to be present. */
    if (false === $this->get("cache")->hasStateValidator($request)) {
        $method = strtoupper($request->getMethod());
        return (new PreconditionRequiredResponseFactory)->create(
            "{$method} request is required to be conditional",
            428
        );
    }

    /* If-Unmodified-Since and If-Match request header handling. If in the meanwhile  */
    /* someone has modified the todo respond with 412 Precondition Failed. */
    if (false === $this->get("cache")->hasCurrentState($request, $todo->etag(), $todo->timestamp())) {
        return (new PreconditionFailedResponseFactory)->create(
            "Todo has already been modified",
            412
        );
    }

    $data = json_decode($request->getBody(), true) ?: [];

    /* PUT request assumes full representation. PATCH allows partial data. */
    if ("PUT" === strtoupper($request->getMethod())) {
        $command = new ReplaceTodoCommand(
            $uid,
            $data["title"],
            $data["order"],
            $data["completed"]
        );
    } else {
        $command = new UpdateTodoCommand(
            $uid,
            $data["title"] ?? $todo->title(),
            $data["order"] ?? $todo->order(),
            $data["completed"] ?? $todo->isCompleted()
        );
    }
    $this->get("commandBus")->handle($command);

    $query = new ReadTodoQuery($uid);
    $todo = $this->get("commandBus")->handle($query);

    /* Add Last-Modified and ETag headers to response. */
    $response = $this->get("cache")->withEtag($response, $todo->etag());
    $response = $this->get("cache")->withLastModified($response, $todo->timestamp());

    $data = $this->get("transformTodoService")->execute($todo);
    $body = $response->getBody();
    $body->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->withBody($body);
});

$app->delete("/todos/{uid}", function ($request, $response, $arguments) {

    /* Check if token has needed scope. */
    if (false === $this->get("token")->hasScope(["todo.all", "todo.delete"])) {
        return (new ForbiddenResponseFactory)->create(
            "Token not allowed to delete todos",
            403
        );
    }

    $uid = new TodoUid($arguments["uid"]);

    try {
        $command = new DeleteTodoCommand($uid);
        $todo = $this->get("commandBus")->handle($command);
    } catch (TodoNotFoundException $exception) {
        return (new NotFoundResponseFactory)->create("Todo not found", 404);
    }

    return $response->withStatus(204);
});
