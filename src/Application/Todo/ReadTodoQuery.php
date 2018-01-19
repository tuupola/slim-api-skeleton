<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

class ReadTodoQuery
{
    private $uid;

    public function __construct(array $data = [])
    {
        $this->setUid($data["uid"]);
    }

    public function uid()
    {
        return $this->uid;
    }

    private function setUid(string $uid): void
    {
        $this->uid = $uid;
    }
}
