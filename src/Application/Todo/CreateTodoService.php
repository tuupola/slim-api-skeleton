<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Datetime;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class CreateTodoService
{
    private $repository;
    private $hydrator;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function execute(array $request = []): Todo
    {
        $request["uid"] = $this->repository->nextIdentity();
        $request["created_at"] = new DateTime;
        $request["updated_at"] = $request["created_at"];

        $todo = $this->hydrator->hydrate($request, new Todo);
        $this->repository->add($todo);

        return $todo;
    }
}
