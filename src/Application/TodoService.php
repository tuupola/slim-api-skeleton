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

    public function get(array $params): Todo
    {
        return $this->repository->get($params["uid"]);
    }

    public function create(array $params): Todo
    {
        return new Todo($params);
    }

    public function save(Todo $Todo): bool
    {
        return $this->repository->save($todo);
    }

    public function delete(Todo $todo): bool
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
