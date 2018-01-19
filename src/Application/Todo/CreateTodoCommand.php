<?php

declare(strict_types=1);

namespace Skeleton\Application\Todo;

class CreateTodoCommand
{
    private $order;
    private $completed = false;
    private $title;

    public function __construct(array $data = [])
    {
        $this->setUid($data["uid"]);
        $this->setOrder($data["order"]);
        $this->setTitle($data["title"]);
    }

    private function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    private function setOrder(int $order): void
    {
        $this->order = $order;
    }

    private function setTitle(string $title): void
    {
        $this->title = $title;
    }

    private function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getArrayCopy(): array
    {
        return [
            "uid" => $this->uid,
            "order" => $this->order,
            "completed" => $this->completed,
            "title" => $this->title,
        ];
    }
}
