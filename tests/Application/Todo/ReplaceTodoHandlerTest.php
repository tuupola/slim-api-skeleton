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

    protected function setUp()
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
        $create = new CreateTodoCommand([
            "uid" => $uid,
            "title" => "Not sure?",
            "order" => 27
        ]);
        $this->createTodoHandler->handle($create);

        $read = new ReadTodoQuery([
            "uid" => $uid,
        ]);
        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Not sure?", $todo->title());

        $todo->complete();
        $replace = new ReplaceTodoCommand([
            "uid" => $uid,
            "order" => 1,
        ]);

        $this->replaceTodoHandler->handle($replace);

        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals(null, $todo->title());
        $this->assertEquals(1, $todo->order());
        $this->assertEquals($uid, $todo->uid());
    }
}
