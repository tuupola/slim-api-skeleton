<?php

namespace Skeleton\Application;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;

use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Serializer\DataArraySerializer;

class TodoService
{
    private $repository;

    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(string $uid): Todo
    {
        return $this->repository->get($uid);
    }

    public function create(array $params): Todo
    {
        $todo = new Todo($params);
        $this->repository->save($todo);
        return $todo;
    }

    public function save(Todo $Todo): bool
    {
        return $this->repository->save($todo);
    }

    public function remove(Todo $todo): bool
    {
        return $this->repository->delete($todo);
    }

    public function transform(Todo $todo): array
    {
        $fractal = new FractalManager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new FractalItem($todo, new TodoTransformer);
        return $fractal->createData($resource)->toArray();
    }
}
