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

use Skeleton\Domain\TodoUid;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use Laminas\Hydrator\Strategy\ClosureStrategy;

class TodoHydratorFactory
{
    public function create(): HydratorInterface
    {
        $hydrator = new ReflectionHydrator;
        $hydrator->setNamingStrategy(MapNamingStrategy::createFromHydrationMap([
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
            new ClosureStrategy(
                function ($completed) {
                    return in_array($completed, [1, "1", "true", true], true);
                },
                function ($completed) {
                    return in_array($completed, [1, "1", "true", true], true);
                }
            )
        );
        $hydrator->addStrategy(
            "uid",
            new ClosureStrategy(
                function (TodoUid $uid) {
                    return (string) $uid;
                },
                function ($uid) {
                    return new TodoUid((string)$uid);
                }
            )
        );
        /* For Zend DB everything is a string, need to typejuggle. */
        $hydrator->addStrategy(
            "order",
            new ClosureStrategy(
                function (int $order) {
                    return $order;
                },
                function ($order) {
                    return (int) $order;
                }
            )
        );

        return $hydrator;
    }
}
