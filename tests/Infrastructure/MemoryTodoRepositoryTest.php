<?php

namespace Skeleton\Infrastructure;

use PHPUnit\Framework\TestCase;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;

class MemoryTodoRepositoryTest extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGetNextIdentity()
    {
        $repository = new MemoryTodoRepository;
        $uid = $repository->nextIdentity();
        $this->assertInstanceOf(TodoUid::class, $uid);
    }

    public function testGetShouldThrowNotFound()
    {
        $this->expectException(TodoNotFoundException::class);
        $repository = new MemoryTodoRepository;
        $uid = $repository->nextIdentity();
        $repository->get($uid);
    }

    public function testFirstShouldThrowNotFound()
    {
        $this->expectException(TodoNotFoundException::class);
        $repository = new MemoryTodoRepository;
        $repository->first();
    }

    public function testShouldAddTodo()
    {
        $repository = new MemoryTodoRepository;
        $this->assertEquals(0, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo(["uid" => $uid]);
        $repository->add($todo);
        $this->assertEquals(1, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo(["uid" => $uid]);
        $repository->add($todo);
        $this->assertEquals(2, $repository->count());
    }

    public function testShouldRemoveTodo()
    {
        $repository = new MemoryTodoRepository;

        $uid = $repository->nextIdentity();
        $todo1 = new Todo(["uid" => $uid]);
        $repository->add($todo1);

        $uid = $repository->nextIdentity();
        $todo2 = new Todo(["uid" => $uid]);
        $repository->add($todo2);

        $this->assertEquals(2, $repository->count());

        $repository->remove($todo1);
        $this->assertEquals(1, $repository->count());
        $this->assertfalse($repository->contains($todo1));
        $this->assertTrue($repository->contains($todo2));
    }

    public function testShouldGetAll()
    {
        $repository = new MemoryTodoRepository;
        $this->assertEquals(0, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo(["uid" => $uid]);
        $repository->add($todo);
        $this->assertEquals(1, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo(["uid" => $uid]);
        $repository->add($todo);

        $all = $repository->all();
        $this->assertEquals(2, count($all));
        $this->assertInstanceOf(Todo::class, $all[0]);
        $this->assertInstanceOf(Todo::class, $all[1]);
    }
}
