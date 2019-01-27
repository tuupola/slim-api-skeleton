<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\TodoUid;

class ReadTodoQuery
{
    private $uid;

    public function __construct(TodoUid $uid)
    {
        $this->uid = $uid;
    }

    public function uid(): TodoUid
    {
        return $this->uid;
    }
}
