<?php

namespace Response;

use PHPUnit\Framework\TestCase;

class PreconditionRequiredResponseTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }
    public function testShouldBeProblemJson()
    {
        $response = new PreconditionRequiredResponse("Yo! MTV Raps");
        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-type")
        );
    }
}
