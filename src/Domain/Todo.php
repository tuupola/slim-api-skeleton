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
    private $order;
    private $completed = false;
    private $title;
    private $createdAt = null;
    private $updatedAt = null;

    public function __construct(array $data = [])
    {
        $this->populate($data);
    }

    public function uid(): string
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
        $data = $this->getArrayCopy();
        return md5(serialize($data));
    }

     /**
     * Reset all properties to default values.
     */
    public function reset(): void
    {
        $this->populate([
            "order" => 0,
            "completed" => false,
            "title" => null,
        ]);
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTime;
    }

    /**
     * Populate all properties from the given array.
     */
    private function populate(array $data = []): void
    {
        foreach ($data as $key => $value) {
            /* https://github.com/facebook/hhvm/issues/6368 */
            $key = str_replace("_", " ", $key);
            $method = "set" . ucwords($key);
            $method = str_replace(" ", "", $method);
            if (method_exists($this, $method)) {
                /* Try to use setter */
                call_user_func([$this, $method], $value);
            } else {
                /* Or fallback to setting option directly */
                if (property_exists(self::class, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    private function setCreatedAt(?DateTime $datetime): void
    {
        $this->createdAt = $datetime ?? new DateTime;
    }

    private function setUpdatedAt(?DateTime $datetime): void
    {
        $this->updatedAt = $datetime ?? new DateTime;
    }
}
