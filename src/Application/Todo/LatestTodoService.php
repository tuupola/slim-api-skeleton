<?php

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class LatestTodoService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $request = []): Todo
    {
        return $this->repository->first($request);
    }
}
