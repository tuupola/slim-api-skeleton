<?php

declare(strict_types=1);

namespace Skeleton\Application;

use Skeleton\Domain\TransformerInterface;

use function Functional\map;

final class CollectionTransformer implements TransformerInterface
{
    private $transformer;

    public function __construct(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function transform($items): array
    {
        return map($items, function ($item) {
            return $this->transformer->transform($item);
        });
    }
}
