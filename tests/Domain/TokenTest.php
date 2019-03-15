<?php

namespace Skeleton\Domain;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldCheckScope()
    {
        $token = new Token([
            "scope" => ["todo.read", "todo.write"]
        ]);

        $this->assertTrue($token->hasScope(["todo.read"]));
        $this->assertFalse($token->hasScope(["todo.delete"]));
    }
}
