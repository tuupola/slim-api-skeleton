<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use Spot\EntityInterface;
use Spot\MapperInterface;
use Spot\EventEmitter;

use Ramsey\Uuid\Uuid;
use Psr\Log\LogLevel;

class Todo extends \Spot\Entity
{
    protected static $table = "todos";

    public static function fields()
    {
        return [
            "id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
            "order" => ["type" => "integer", "unsigned" => true, "value" => 0],
            "uuid" => ["type" => "string", "length" => 36],
            "title" => ["type" => "string", "length" => 255],
            "completed" => ["type" => "boolean", "value" => false],
            "created_at"   => ["type" => "datetime", "value" => new \DateTime()],
            "updated_at"   => ["type" => "datetime", "value" => new \DateTime()]
        ];
    }

    public static function events(EventEmitter $emitter)
    {
        $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
            $uuid = Uuid::uuid1();
            $entity->uuid = $uuid->toString();
        });

        $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
           $entity->updated_at = new \DateTime();
       });
    }
    public function timestamp()
    {
        return $this->updated_at->getTimestamp();
    }

    public function etag()
    {
        return md5($this->uuid . $this->timestamp());
    }
}