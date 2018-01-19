<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class TransformTodoServiceTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
        $this->transformTodoService = new TransformTodoService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldTransformTodo()
    {
        $command = new CreateTodoCommand([
            "uid" => $this->todoRepository->nextIdentity(),
            "title" => "Not sure?",
            "order" => 27,
        ]);
        $this->createTodoHandler->handle($command);
        $todo = $this->latestTodoHandler->handle();

        $transformed =$this->transformTodoService->execute($todo);

        $this->assertArrayHasKey("uid", $transformed["data"]);
        $this->assertArrayHasKey("order", $transformed["data"]);
        $this->assertArrayHasKey("title", $transformed["data"]);
        $this->assertArrayHasKey("completed", $transformed["data"]);
        $this->assertArrayHasKey("links", $transformed["data"]);
    }
}
