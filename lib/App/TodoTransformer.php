<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use App\Todo;
use League\Fractal;

class TodoTransformer extends Fractal\TransformerAbstract
{

    public function transform(Todo $todo)
    {
        return [
            "uid" => (string)$todo->uid ?: null,
            "order" => (integer)$todo->order ?: 0,
            "title" => (string)$todo->title ?: null,
            "completed" => !!$todo->completed,
            "links"        => [
                "self" => "/todos/{$todo->uid}"
            ]
        ];
    }
}
