<?php

namespace Skeleton\Infrastructure\Slim\Handler;

use PHPUnit\Framework\TestCase;
use Slim\CallableResolver;
use Slim\Exception\HttpMethodNotAllowedException;
use Tuupola\Http\Factory\ServerRequestFactory;
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
        $request = (new ServerRequestFactory)->createServerRequest("GET", "https://example.com/");
        $response = (new ResponseFactory)->createResponse();
        $exception = new HttpMethodNotAllowedException($request);

        $handler = new ApiErrorHandler(
            new CallableResolver,
            new ResponseFactory
        );

        $response = $handler($request, $exception, false, false, false);

        $this->assertEquals(
            "application/problem+json",
            $response->getHeaderLine("Content-Type")
        );

        $this->assertEquals(405, $response->getStatusCode());
    }
}
