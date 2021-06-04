<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReplaceTodoHandlerTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $replaceTodoHandler;
    private $readTodoHandler;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->replaceTodoHandler = new ReplaceTodoHandler($this->todoRepository);
        $this->readTodoHandler = new ReadTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldReplaceTodo()
    {
        $uid = $this->todoRepository->nextIdentity();
        $create = new CreateTodoCommand(
            $uid,
            "Not sure?"
        );
        $this->createTodoHandler->handle($create);

        $read = new ReadTodoQuery($uid);
        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Not sure?", $todo->title());

        $todo->complete();
        $replace = new ReplaceTodoCommand(
            $uid,
            "Really?",
            1,
            true
        );

        $this->replaceTodoHandler->handle($replace);

        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Really?", $todo->title());
        $this->assertEquals(1, $todo->order());
        $this->assertEquals($uid, $todo->uid());
    }
}
