<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class TransformTodoCollectionServiceTest extends TestCase
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
        $this->transformTodoCollectionService = new TransformTodoCollectionService($this->todoRepository);
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
        $transformed =$this->transformTodoCollectionService->execute($collection);

        $this->assertCount(2, $transformed["data"]);
        $this->assertArrayHasKey("uid", $transformed["data"][0]);
        $this->assertArrayHasKey("order", $transformed["data"][0]);
        $this->assertArrayHasKey("title", $transformed["data"][0]);
        $this->assertArrayHasKey("completed", $transformed["data"][0]);
        $this->assertArrayHasKey("links", $transformed["data"][0]);
    }
}
