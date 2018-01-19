<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;

class UpdateTodoCommand
{
    private $data;

    public function __construct(array $data)
    {
        $this->setData($data);
    }

    public function uid()
    {
        return $this->data["uid"];
    }

    private function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getArrayCopy(): array
    {
        return $this->data;
    }
}
