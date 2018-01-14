<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class TransformTodoCollectionServiceTest extends TestCase
{
    private $todoRepository;
    private $createTodoService;
    private $transformTodoCollectionService;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->readTodoCollectionService = new ReadTodoCollectionService($this->todoRepository);
        $this->transformTodoCollectionService = new TransformTodoCollectionService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldTransformTodo()
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
        $transformed =$this->transformTodoCollectionService->execute($collection);

        $this->assertCount(2, $transformed["data"]);
        $this->assertArrayHasKey("uid", $transformed["data"][0]);
        $this->assertArrayHasKey("order", $transformed["data"][0]);
        $this->assertArrayHasKey("title", $transformed["data"][0]);
        $this->assertArrayHasKey("completed", $transformed["data"][0]);
        $this->assertArrayHasKey("links", $transformed["data"][0]);
    }
}
