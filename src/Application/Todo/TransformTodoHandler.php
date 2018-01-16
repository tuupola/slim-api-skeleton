<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Serializer\DataArraySerializer;

class TransformTodoHandler
{
    public function handle(Todo $todo): array
    {
        $fractal = new FractalManager();
        $fractal->setSerializer(new DataArraySerializer);
        $resource = new FractalItem($todo, new TodoTransformer);
        return $fractal->createData($resource)->toArray();
    }
}
