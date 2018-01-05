<?php

namespace Skeleton\Application;

use RuntimeException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ViewTodoService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $request): Todo
    {
        $todo = $this->repository->get($request["uid"]);
        // if (null === $user) {
        //     throw new RuntimeException("User {$request['uid']} does not exist.");
        // }
        return $todo;
    }
}
