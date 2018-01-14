<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class DeleteTodoServiceTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->deleteTodoService = new DeleteTodoService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldDeleteTodo()
    {
        $todo = $this->createTodoService->execute([
            "title" => "Not sure?",
            "order" => 27,
        ]);

        $this->assertTrue($this->todoRepository->contains($todo));
        $this->deleteTodoService->execute(["uid" => $todo->uid()]);
        $this->assertFalse($this->todoRepository->contains($todo));
    }
}
