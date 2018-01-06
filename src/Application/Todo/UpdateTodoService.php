<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use RuntimeException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

class UpdateTodoService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $request = []): Todo
    {
        $todo = $this->repository->get($request["uid"]);
        if (null === $todo) {
            throw new RuntimeException("Todo {$request['uid']} does not exist.");
        }
        $todo->populate($request);
        $this->repository->add($todo);
        return $todo;
    }
}
