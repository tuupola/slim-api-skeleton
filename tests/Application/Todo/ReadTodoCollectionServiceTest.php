<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReadTodoCollectionServiceTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->readTodoCollectionService = new ReadTodoCollectionService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldReadTodo()
    {
        $first = $this->createTodoService->execute([
            "title" => "Not sure?",
            "order" => 27,
        ]);

        $second = $this->createTodoService->execute([
            "title" => "Brawndo!",
            "order" => 66,
        ]);

        $collection = $this->readTodoCollectionService->execute();

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Todo::class, $collection[0]);
        $this->assertInstanceOf(Todo::class, $collection[1]);
    }
}
