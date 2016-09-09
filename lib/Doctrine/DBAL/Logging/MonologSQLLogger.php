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

namespace Doctrine\DBAL\Logging;

class MonologSQLLogger implements SQLLogger
{

    public $logger;
    public $sql = "";
    public $start = null;

    public function __construct($logger = null)
    {
        $this->logger = $logger;
    }


    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);

        $this->sql = preg_replace_callback("/\?/", function ($matches) use (&$params, &$types) {
            $param = array_shift($params);
            if (null === $param) {
                return "NULL";
            } else {
                return "'" . $param . "'";
            }
        }, $sql);
    }

    public function stopQuery()
    {
        $elapsed = microtime(true) - $this->start;
        $this->sql .= " -- {$elapsed}";
        $this->logger->debug($this->sql);
    }
}
