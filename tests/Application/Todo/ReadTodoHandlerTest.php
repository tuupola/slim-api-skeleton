<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReadTodoHandlerTest extends TestCase
{
    private $todoRepository;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->readTodoHandler = new ReadTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldReadTodo()
    {
        $uid = $this->todoRepository->nextIdentity();
        $command = new CreateTodoCommand(
            $uid,
            "Not sure?"
        );
        $this->createTodoHandler->handle($command);

        $query = new ReadTodoQuery($uid);
        $todo = $this->readTodoHandler->handle($query);

        $this->assertEquals("Not sure?", $todo->title());
    }
}
