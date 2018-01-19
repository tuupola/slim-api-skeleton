<?php

namespace Skeleton\Application\Response;

use PHPUnit\Framework\TestCase;

class PreconditionFailedResponseTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }
    public function testShouldBeProblemJson()
    {
        $response = new PreconditionFailedResponse("Yo! MTV Raps");
        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-type")
        );
    }
}
