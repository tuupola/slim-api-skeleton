<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class ReadTodoCollectionHandlerTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $readTodoCollectionHandler;

    protected function setUp(): void
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
        $this->assertEquals("Not sure?", $collection[0]->title());
        $this->assertEquals("Brawndo!", $collection[1]->title());
    }
}
