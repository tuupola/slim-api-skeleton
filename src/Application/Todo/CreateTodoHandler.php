<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Datetime;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class CreateTodoHandler
{
    private $repository;
    private $hydrator;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function handle(CreateTodoCommand $command): void
    {
        $data = $command->asArray();
        $data["created_at"] = (new DateTime)->format("Y-m-d H:i:s");
        $data["updated_at"] = $data["created_at"];

        $todo = $this->hydrator->hydrate($data, new Todo);
        $this->repository->add($todo);
    }
}
