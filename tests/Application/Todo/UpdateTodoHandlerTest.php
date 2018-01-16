<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class UpdateTodoHandlerTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $updateTodoHandler;
    private $readTodoHandler;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->updateTodoHandler = new UpdateTodoHandler($this->todoRepository);
        $this->readTodoHandler = new ReadTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldUpdateTodo()
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
        $update = new UpdateTodoCommand([
            "uid" => $uid,
            "title" => "Like from toilet?",
        ]);

        $this->updateTodoHandler->handle($update);

        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Like from toilet?", $todo->title());
        $this->assertEquals(27, $todo->order());
        $this->assertEquals($uid, $todo->uid());
        var_dump($todo);
        $this->assertFalse($todo->isCompleted());
    }
}
