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

    protected function setUp(): void
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
        $create = new CreateTodoCommand($uid, "Not sure?", 20);
        $this->createTodoHandler->handle($create);

        $read = new ReadTodoQuery($uid);
        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Not sure?", $todo->title());
        $this->assertEquals(20, $todo->order());
        $this->assertEquals($uid, $todo->uid());

        $todo->complete();
        $update = new UpdateTodoCommand($uid, "Like from toilet?", 27, true);

        $this->updateTodoHandler->handle($update);

        $todo = $this->readTodoHandler->handle($read);
        $this->assertEquals("Like from toilet?", $todo->title());
        $this->assertEquals(27, $todo->order());
        $this->assertEquals($uid, $todo->uid());
    }
}
