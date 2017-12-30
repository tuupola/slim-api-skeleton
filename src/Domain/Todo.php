<?php

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

class Todo
{
    private $id;
    private $uid;
    private $order;
    private $completed;
    private $title;
    private $createdAt;
    private $updatedAt;

    public function __construct(array $data = [])
    {
        //$this->hydrate($data);
    }

    public function uid()
    {
        return $this->uid;
    }

    public function order()
    {
        return $this->order;
    }

    public function changeOrder(int $order)
    {
        return $this->order = $order;
    }

    public function isCompleted()
    {
        return $this->completed;
    }

    public function complete()
    {
        return $this->completed = true;
    }

    public function commence()
    {
        return $this->completed = false;
    }

    public function title()
    {
        return $this->title;
    }

    public function changeTitle(string $title)
    {
        return $this->title = $title;
    }

    public function timestamp()
    {
        return $this->updatedAt->getTimestamp();
    }

    public function etag()
    {
        return md5($this->uid . $this->timestamp());
    }

    /**
     * Hydrate all options from the given array.
     */
    // private function hydrate(array $data = []): void
    // {
    //     foreach ($data as $key => $value) {
    //         /* https://github.com/facebook/hhvm/issues/6368 */
    //         $key = str_replace("_", " ", $key);
    //         $method = "set" . ucwords($key);
    //         $method = str_replace(" ", "", $method);
    //         if (method_exists($this, $method)) {
    //             /* Try to use setter */
    //             call_user_func([$this, $method], $value);
    //         } else {
    //             /* Or fallback to setting option directly */
    //             if (property_exists(self::class, $key)) {
    //                 $this->$key = $value;
    //             }
    //         }
    //     }
    // }

    // private function setCreatedAt($datetime)
    // {
    //     $this->createdAt = $datetime;
    // }

    // private function setUpdatedAt($datetime)
    // {
    //     $this->updatedAt = $datetime;
    // }
}
