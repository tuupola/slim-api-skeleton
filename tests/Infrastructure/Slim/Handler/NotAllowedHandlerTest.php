<?php

namespace Skeleton\Infrastructure\Slim\Handler;

use PHPUnit\Framework\TestCase;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;

class NotAllowedHandlerTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldInvoke()
    {
        $request = (new RequestFactory)->createRequest("GET", "https://example.com/");
        $response = (new ResponseFactory)->createResponse();

        $handler = new NotAllowedHandler;

        $response = $handler($request, $response, ["PUT", "PATCH", "DELETE"]);

        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-Type")
        );

        $this->assertEquals(405, $response->getStatusCode());
    }
}
