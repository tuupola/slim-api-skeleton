<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

class CreateTodoCommand
{
    private $uid;
    private $title;
    private $completed;
    private $order;

    public function __construct(
        string $uid,
        string $title,
        int $order = 0,
        bool $completed = false
    ) {
        $this->uid = $uid;
        $this->title = $title;
        $this->order = $order;
        $this->completed = $completed;
    }

    public function uid(): string
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
            "uid" => $this->uid,
            "order" => $this->order,
            "completed" => $this->completed,
            "title" => $this->title,
        ];
    }
}
