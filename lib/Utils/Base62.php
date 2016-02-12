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

class Base62
{

    public static function encode($data)
    {
        $outstring = "";
        $l = strlen($data);
        for ($i = 0; $i < $l; $i += 8) {
            $chunk = substr($data, $i, 8);
            $outlen = ceil((strlen($chunk) * 8)/6);
            $x = bin2hex($chunk);
            $w = gmp_strval(gmp_init(ltrim($x, "0"), 16), 62);
            $pad = str_pad($w, $outlen, "0", STR_PAD_LEFT);
            $outstring .= $pad;
        }
        return $outstring;
    }

    public static function decode($data)
    {
        $outstring = "";
        $l = strlen($data);
        for ($i = 0; $i < $l; $i += 11) {
            $chunk = substr($data, $i, 11);
            $outlen = floor((strlen($chunk) * 6)/8);
            $y = gmp_strval(gmp_init(ltrim($chunk, "0"), 62), 16);
            $pad = str_pad($y, $outlen * 2, "0", STR_PAD_LEFT);
            $outstring .= pack("H*", $pad);
        }
        return $outstring;
    }
}
