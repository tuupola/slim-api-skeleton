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

    public function testShouldConstruct()
    {
        $todo = new Todo(new TodoUid, "Not sure?", 99, true);
        $this->assertEquals("Not sure?", $todo->title());
        $this->assertEquals(99, $todo->order());
        $this->assertTrue($todo->isCompleted());
    }

    public function testShouldSetDefaultValues()
    {
        $todo = new Todo(new TodoUid, "Not sure?");
        $this->assertEquals(0, $todo->order());
        $this->assertFalse($todo->isCompleted());
    }

    public function testShouldGenerateEtag()
    {
        $todo = new Todo(new TodoUid, "Not sure?");
        $this->assertEquals(32, strlen($todo->etag()));
    }
}
