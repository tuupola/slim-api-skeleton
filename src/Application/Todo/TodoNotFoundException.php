<?php
declare(strict_types=1);

namespace Skeleton\Application\Todo;

use Exception;

class TodoNotFoundException extends Exception
{
    protected $message = "Todo not found";
    protected $code = 404;
}
