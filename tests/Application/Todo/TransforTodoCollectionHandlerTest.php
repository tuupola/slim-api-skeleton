<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class TransformTodoCollectionHandlerTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $readTodoCollectionHandler;
    private $transformTodoCollectionHandler;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->readTodoCollectionHandler = new ReadTodoCollectionHandler($this->todoRepository);
        $this->transformTodoCollectionHandler = new TransformTodoCollectionHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldTransformTodo()
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
        $transformed =$this->transformTodoCollectionHandler->handle($collection);

        $this->assertCount(2, $transformed["data"]);
        $this->assertArrayHasKey("uid", $transformed["data"][0]);
        $this->assertArrayHasKey("order", $transformed["data"][0]);
        $this->assertArrayHasKey("title", $transformed["data"][0]);
        $this->assertArrayHasKey("completed", $transformed["data"][0]);
        $this->assertArrayHasKey("links", $transformed["data"][0]);
    }
}
