<?php

namespace Exception;

class ForbiddenException extends \Exception
{
    public function httpStatus()
    {
        return 403;
    }
}
