<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class UpdateTodoServiceTest extends TestCase
{
    private $todoRepository;
    private $createTodoService;
    private $updateTodoService;
    private $readTodoService;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->updateTodoService = new UpdateTodoService($this->todoRepository);
        $this->readTodoService = new ReadTodoService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldUpdateTodo()
    {
        $todo = $this->createTodoService->execute([
            "title" => "Not sure?",
            "order" => 27,
        ]);
        $this->assertFalse($todo->isCompleted());
        $todo->complete();

        $this->updateTodoService->execute($todo);
        $updated = $this->readTodoService->execute(["uid" => $todo->uid()]);
        $this->assertTrue($updated->isCompleted());
    }
}
