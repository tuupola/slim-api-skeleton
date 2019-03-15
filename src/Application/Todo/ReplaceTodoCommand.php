<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;

class ReplaceTodoCommand
{
    private $uid;
    private $title;
    private $completed;
    private $order;

    public function __construct(
        TodoUid $uid,
        string $title,
        int $order,
        bool $completed
    ) {
        $this->uid = $uid;
        $this->title = $title;
        $this->order = $order;
        $this->completed = $completed;
    }

    public function uid(): TodoUid
    {
        return $this->uid;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function completed(): bool
    {
        return $this->completed;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function asArray(): array
    {
        return [
            "uid" => $this->uid(),
            "order" => $this->order(),
            "completed" => $this->completed(),
            "title" => $this->title(),
        ];
    }
}
