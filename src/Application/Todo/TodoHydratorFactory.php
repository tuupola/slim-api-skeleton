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

namespace Skeleton\Application\Todo;

use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\NamingStrategy\MapNamingStrategy;
#use Zend\Hydrator\Reflection as ReflectionHydrator;
use Zend\Hydrator\ArraySerializable as ArraySerializableHydrator;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
#use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\Strategy\BooleanStrategy;

class TodoHydratorFactory
{
    public function create(): HydratorInterface
    {
        $hydrator = new ArraySerializableHydrator;
        $hydrator->setNamingStrategy(new MapNamingStrategy([
            "created_at" => "createdAt",
            "updated_at" => "updatedAt",
        ]));
        $hydrator->addStrategy(
            "createdAt",
            new DateTimeFormatterStrategy("Y-m-d H:i:s")
        );
        $hydrator->addStrategy(
            "updatedAt",
            new DateTimeFormatterStrategy("Y-m-d H:i:s")
        );
        $hydrator->addStrategy(
            "completed",
            new BooleanStrategy(0, 1)
        );

        return $hydrator;
    }
}
