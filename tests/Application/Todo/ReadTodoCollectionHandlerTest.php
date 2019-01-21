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
        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Not sure?"
        );
        $this->createTodoHandler->handle($command);

        $command = new CreateTodoCommand(
            $this->todoRepository->nextIdentity(),
            "Brawndo!"
        );
        $this->createTodoHandler->handle($command);

        $collection = $this->readTodoCollectionHandler->handle();

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Todo::class, $collection[0]);
        $this->assertInstanceOf(Todo::class, $collection[1]);
    }
}
