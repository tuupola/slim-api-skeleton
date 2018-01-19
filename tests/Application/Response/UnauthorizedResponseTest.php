<?php

namespace Skeleton\Application\Response;

use PHPUnit\Framework\TestCase;

class UnauthorizedResponseTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }
    public function testShouldBeProblemJson()
    {
        $response = new UnauthorizedResponse("Yo! MTV Raps");
        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-type")
        );
    }
}
