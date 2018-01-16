<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use RuntimeException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ReadTodoHandler
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ReadTodoQuery $command): Todo
    {
        return $this->repository->get($command->uid());
    }
}
