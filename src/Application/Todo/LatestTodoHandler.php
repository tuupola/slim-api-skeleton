<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class LatestTodoHandler
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(): Todo
    {
        return $this->repository->last();
    }
}
