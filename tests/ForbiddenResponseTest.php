<?php

namespace Response;

use PHPUnit\Framework\TestCase;

class ForbiddenResponseTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }
    public function testShouldBeProblemJson()
    {
        $response = new ForbiddenResponse("Yo! MTV Raps");
        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-type")
        );
    }
}
