<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;

class TodoTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testGenerateEtag()
    {
        $todo = new Todo;
        $this->assertEquals(32, strlen($todo->etag()));
    }
}
