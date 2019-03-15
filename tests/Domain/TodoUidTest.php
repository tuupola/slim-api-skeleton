<?php

namespace Skeleton\Domain;

use PHPUnit\Framework\TestCase;

class TodoUidTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldCompare()
    {
        $uid1 = new TodoUid;
        $uid2 = new TodoUid;
        $uid3 = new TodoUid((string) $uid2);

        $this->assertFalse($uid1->equals($uid2));
        $this->assertTrue($uid2->equals($uid3));
        $this->assertEquals($uid2->uid(), $uid3->uid());
    }
}
