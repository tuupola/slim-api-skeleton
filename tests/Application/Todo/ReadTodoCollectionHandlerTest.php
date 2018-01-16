<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReadTodoCollectionServiceTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $readTodoCollectionHandler;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->readTodoCollectionHandler = new ReadTodoCollectionHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldReadTodo()
    {
        $command = new CreateTodoCommand([
            "uid" => $this->todoRepository->nextIdentity(),
            "title" => "Not sure?",
            "order" => 27,
        ]);
        $this->createTodoHandler->handle($command);

        $command = new CreateTodoCommand([
            "uid" => $this->todoRepository->nextIdentity(),
            "title" => "Brawndo!",
            "order" => 66,
        ]);
        $this->createTodoHandler->handle($command);

        $collection = $this->readTodoCollectionHandler->handle();

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Todo::class, $collection[0]);
        $this->assertInstanceOf(Todo::class, $collection[1]);
    }
}
