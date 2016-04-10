<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2011-2016 Marcus Bointon, Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://gist.github.com/Synchro/1139429
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace Utils;

class Url64
{

    public static function encode($data)
    {
        return strtr(
            base64_encode($data),
            "+/",
            "-_"
        );
    }

    public static function decode($data)
    {
        return base64_decode(
            strtr($data, "-_", "+/")
        );
    }
}
