<?php

namespace Skeleton\Application;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ViewTodosService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $request = []): array
    {
        return $this->repository->query($request);
    }
}
