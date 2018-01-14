<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReadTodoServiceTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->readTodoService = new ReadTodoService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldReadTodo()
    {
        $created = $this->createTodoService->execute([
            "title" => "Not sure?",
            "order" => 27,
        ]);

        $read = $this->readTodoService->execute(["uid" => $created->uid()]);

        $this->assertEquals($created, $read);
    }
}
