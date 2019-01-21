<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class CreateTodoHandlerTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldCreateTodo()
    {
        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Not sure?",
        );
        $this->createTodoHandler->handle($command);
        $todo = $this->latestTodoHandler->handle();

        $this->assertInstanceOf(Todo::class, $todo);
    }
}
