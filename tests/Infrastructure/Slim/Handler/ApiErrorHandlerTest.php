<?php

namespace Skeleton\Infrastructure\Slim\Handler;

use Exception;
use PHPUnit\Framework\TestCase;
use Tuupola\Http\Factory\RequestFactory;
use Tuupola\Http\Factory\ResponseFactory;
use Psr\Log\NullLogger;

class ApiErrorHandlerTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldInvoke()
    {
        $request = (new RequestFactory)->createRequest("GET", "https://example.com/");
        $response = (new ResponseFactory)->createResponse();
        $exception = new Exception("Test");

        $handler = new ApiErrorHandler(new NullLogger);

        $response = $handler($request, $response, $exception);

        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-Type")
        );

        $this->assertEquals(500, $response->getStatusCode());
    }
}
