<?php

namespace Skeleton\Domain;

use PHPUnit\Framework\TestCase;

class TodoTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldConstruct()
    {
        $uid = new TodoUid;
        $todo = new Todo($uid, "Brawndo!", 2, false);
        $this->assertEquals($uid, $todo->uid());
        $this->assertEquals("Brawndo!", $todo->title());
        $this->assertEquals(2, $todo->order());
        $this->assertFalse($todo->isCompleted());
    }

    public function testShouldCompleteAndCommence()
    {
        $uid = new TodoUid;
        $todo = new Todo($uid, "Brawndo!", 2, false);
        $this->assertFalse($todo->isCompleted());

        $todo->complete();
        $this->assertTrue($todo->isCompleted());

        $todo->commence();
        $this->assertFalse($todo->isCompleted());
    }

    public function testShouldTouch()
    {
        $uid = new TodoUid;
        $todo = new Todo($uid, "Brawndo!", 2, false);

        $timestamp1 = $todo->timestamp();
        $etag1 = $todo->etag();
        sleep(1);
        $todo->touch();
        $timestamp2 = $todo->timestamp();
        $etag2 = $todo->etag();

        $this->assertGreaterThan($timestamp1, $timestamp2);
        $this->assertNotEquals($etag1, $etag2);
    }
}
