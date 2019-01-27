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

namespace Skeleton\Domain;

use Tuupola\Ksuid;

class TodoUid
{
    private $uid;

    public function __construct(string $uid = null)
    {
        $this->uid = $uid ?: (string) new Ksuid;
    }

    public function uid(): string
    {
        return $this->uid;
    }

    public function __toString(): string
    {
        return $this->uid;
    }
}
