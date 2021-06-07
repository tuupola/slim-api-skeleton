<?php
declare(strict_types=1);

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TransformerInterface;

final class TodoTransformer implements TransformerInterface
{

    public function transform($todo): array
    {
        return [
            "uid" => (string) $todo->uid() ?: null,
            "order" => (integer) $todo->order() ?: 0,
            "title" => (string) $todo->title() ?: null,
            "completed" => (boolean) $todo->isCompleted(),
            "links"        => [
                "self" => "/todos/{$todo->uid()}"
            ]
        ];
    }
}
