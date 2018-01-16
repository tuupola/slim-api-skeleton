<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use RuntimeException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class ReplaceTodoHandler
{
    private $repository;
    private $hydrator;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function handle(ReplaceTodoCommand $command): void
    {
        $data = $command->getArrayCopy();
        $todo = $this->repository->get($command->uid());
        $todo->reset();
        $todo = $this->hydrator->hydrate($data, $todo);
        $todo->touch();
        $this->repository->add($todo);
    }
}
