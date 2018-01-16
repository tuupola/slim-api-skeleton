<?php

namespace Skeleton\Application\Todo;

use PHPUnit\Framework\TestCase;
use Skeleton\Domain\Todo;
use Skeleton\Infrastructure\MemoryTodoRepository;

class LatestTodoHandlerTest extends TestCase
{
    private $todoRepository;

    protected function setUp()
    {
        $this->todoRepository = new MemoryTodoRepository;
        $this->createTodoHandler = new CreateTodoHandler($this->todoRepository);
        $this->latestTodoHandler = new LatestTodoHandler($this->todoRepository);
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGetLatestTodo()
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

        $todo =$this->latestTodoHandler->handle();

        $this->assertEquals("Brawndo!", $todo->title());
    }
}
