<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ReadTodoCollectionHandler
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(): array
    {
        return $this->repository->all();
    }
}
