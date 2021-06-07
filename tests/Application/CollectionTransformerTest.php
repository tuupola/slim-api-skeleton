<?php

namespace Skeleton\Application;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;
use Skeleton\Application\Todo\TodoTransformer;
use Skeleton\Application\Todo\CreateTodoCommand;
use Skeleton\Application\Todo\CreateTodoHandler;
use Skeleton\Application\Todo\ReadTodoCollectionHandler;

class CollectionTransformerTest extends TestCase
{
    private $todoRepository;
    private $createTodoHandler;
    private $readTodoCollectionHandler;
    private $transformTodoCollectionService;

    protected function setUp(): void
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->readTodoCollectionHandler = new ReadTodoCollectionHandler($this->todoRepository);
        $this->transformer = new CollectionTransformer(new TodoTransformer);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldTransformTodo()
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
        $transformed =$this->transformer->transform($collection);

        $this->assertCount(2, $transformed);
        $this->assertArrayHasKey("uid", $transformed[0]);
        $this->assertArrayHasKey("order", $transformed[0]);
        $this->assertArrayHasKey("title", $transformed[0]);
        $this->assertArrayHasKey("completed", $transformed[0]);
        $this->assertArrayHasKey("links", $transformed[0]);
    }
}
