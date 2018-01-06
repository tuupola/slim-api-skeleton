<?php

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Serializer\DataArraySerializer;

class TransformTodoCollectionService
{
    public function execute(array $collection): array
    {
        $fractal = new FractalManager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new FractalCollection($collection, new TodoTransformer);
        return $fractal->createData($resource)->toArray();
    }
}
