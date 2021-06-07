<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class TodoTransformerTest extends TestCase
{
    private $todoRepository;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
        $this->transformer = new TodoTransformer();
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldTransformTodo()
    {
        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Not sure?"
        );
        $this->createTodoHandler->handle($command);
        $todo = $this->latestTodoHandler->handle();

        $transformed =$this->transformer->transform($todo);

        $this->assertArrayHasKey("uid", $transformed);
        $this->assertArrayHasKey("order", $transformed);
        $this->assertArrayHasKey("title", $transformed);
        $this->assertArrayHasKey("completed", $transformed);
        $this->assertArrayHasKey("links", $transformed);
    }
}
