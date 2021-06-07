<?php

declare(strict_types=1);

namespace Skeleton\Domain;

interface TransformerInterface
{
    public function transform($item): array;
}
