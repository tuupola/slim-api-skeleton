<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class LatestTodoHandlerTest extends TestCase
{
    private $todoRepository;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGetLatestTodo()
    {
        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Not sure?"
        );
        $this->createTodoHandler->handle($command);

        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Brawndo!"
        );
        $this->createTodoHandler->handle($command);

        $todo =$this->latestTodoHandler->handle();

        $this->assertEquals("Brawndo!", $todo->title());
    }
}
