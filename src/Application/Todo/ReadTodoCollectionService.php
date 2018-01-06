<?php

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ReadTodoCollectionService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $request = []): array
    {
        return $this->repository->all($request);
    }
}
