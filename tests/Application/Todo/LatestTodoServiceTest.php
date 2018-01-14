<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class LatestTodoServiceTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoService = new CreateTodoService($this->todoRepository);
        $this->latestTodoService = new LatestTodoService($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGetLatestTodo()
    {
        $first = $this->createTodoService->execute([
            "title" => "Not sure?",
            "order" => 27,
        ]);

        $second = $this->createTodoService->execute([
            "title" => "Brawndo!",
            "order" => 66,
        ]);

        $latest =$this->latestTodoService->execute();

        $this->assertEquals($latest, $second);
    }
}
