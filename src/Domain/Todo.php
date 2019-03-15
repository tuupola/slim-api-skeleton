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

use \DateTime;

class Todo
{
    private $uid;
    private $completed;
    private $order;
    private $title;
    private $createdAt = null;
    private $updatedAt = null;

    public function __construct(
        TodoUid $uid,
        string $title,
        int $order = 0,
        bool $completed = false
    ) {
        $this->uid = $uid;
        $this->title = $title;
        $this->order = $order;
        $this->completed = $completed;
        $this->createdAt = new DateTime;
        $this->updatedAt = $this->createdAt;
    }

    public function uid(): TodoUid
    {
        return $this->uid;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function complete(): void
    {
        $this->completed = true;
    }

    public function commence(): void
    {
        $this->completed = false;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function timestamp(): int
    {
        return $this->updatedAt->getTimestamp();
    }

    public function etag(): string
    {
        return md5(serialize($this));
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTime;
    }
}
