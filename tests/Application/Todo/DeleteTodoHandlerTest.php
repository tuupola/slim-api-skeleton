<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class DeleteTodoHandlerTest extends TestCase
{
    private $todoRepository;
    private $deleteTodoHandler;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->deleteTodoHandler = new DeleteTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldDeleteTodo()
    {
        $uid = $this->todoRepository->nextIdentity();

        $command = new CreateTodoCommand(
            $uid,
            "Not sure?"
        );
        $this->createTodoHandler->handle($command);
        $todo = $this->latestTodoHandler->handle();

        $command = new DeleteTodoCommand($uid);

        $this->assertTrue($this->todoRepository->contains($todo));
        $this->deleteTodoHandler->handle($command);
        $this->assertFalse($this->todoRepository->contains($todo));
    }
}
