<?php

namespace Skeleton\Application;

use Skeleton\Application\TodoTransformer;
use Skeleton\Domain\Todo;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Serializer\DataArraySerializer;

class TransformTodoService
{
    public function execute(Todo $todo): array
    {
        $fractal = new FractalManager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new FractalItem($todo, new TodoTransformer);
        return $fractal->createData($resource)->toArray();
    }
}
