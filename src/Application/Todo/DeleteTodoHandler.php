<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use RuntimeException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class DeleteTodoHandler
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(DeleteTodoCommand $command): void
    {
        $todo = $this->repository->get($command->uid());
        $this->repository->remove($todo);
    }
}
