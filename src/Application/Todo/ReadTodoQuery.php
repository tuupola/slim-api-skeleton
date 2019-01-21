<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

class ReadTodoQuery
{
    private $uid;

    public function __construct(string $uid)
    {
        $this->uid = $uid;
    }

    public function uid()
    {
        return $this->uid;
    }
}
