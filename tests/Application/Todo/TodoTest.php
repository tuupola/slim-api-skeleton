<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;

class TodoTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testGenerateEtag()
    {
        $todo = new Todo(new TodoUid, "Not sure?");
        $this->assertEquals(32, strlen($todo->etag()));
    }
}
