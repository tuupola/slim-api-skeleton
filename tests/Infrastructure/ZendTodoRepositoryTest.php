<?php

namespace Skeleton\Infrastructure;

use PDO;
use DateTime;
use Phinx\Console\PhinxApplication;
use Phinx\Config\Config as PhinxConfig;
use Phinx\Migration\Manager as PhinxManager;
use PHPUnit\Framework\TestCase;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class ZendTodoRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        $app = new PhinxApplication();
        $app->setAutoExit(false);
        $app->run(new StringInput("migrate -e testing"), new NullOutput());
    }

    public function tearDown(): void
    {
        unlink("/tmp/skeleton.sqlite3");
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldGetNextIdentity()
    {
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $uid = $repository->nextIdentity();
        $this->assertInstanceOf(TodoUid::class, $uid);
    }

    public function testGetShouldThrowNotFound()
    {
        $this->expectException(TodoNotFoundException::class);
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $uid = $repository->nextIdentity();
        $repository->get($uid);
    }

    public function testFirstShouldThrowNotFound()
    {
        $this->expectException(TodoNotFoundException::class);
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $repository->first();
    }

    public function testLastShouldThrowNotFound()
    {
        $this->expectException(TodoNotFoundException::class);
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $repository->last();
    }

    public function testShouldAddTodo()
    {
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $this->assertEquals(0, $repository->count());

        $uid = $repository->nextIdentity();
        $todo1 = new Todo($uid, "Foo", 1, true);

        $repository->add($todo1);
        $this->assertEquals(1, $repository->count());
        sleep(1);
        $uid = $repository->nextIdentity();
        $todo2 = new Todo($uid, "Bar", 2, false);

        $repository->add($todo2);
        $this->assertEquals(2, $repository->count());

        $first = $repository->first();
        $this->assertEquals($first->uid(), $todo1->uid());
        $this->assertEquals($first->order(), $todo1->order());
        $this->assertEquals($first->isCompleted(), $todo1->isCompleted());
        $this->assertEquals($first->title(), $todo1->title());

        $last = $repository->last();
        $this->assertEquals($last->uid(), $todo2->uid());
        $this->assertEquals($last->order(), $todo2->order());
        $this->assertEquals($last->isCompleted(), $todo2->isCompleted());
        $this->assertEquals($last->title(), $todo2->title());
    }

    public function testShouldUpdateTodo()
    {
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);

        $uid = $repository->nextIdentity();
        $todo1 = new Todo($uid, 1, true);

        $repository->add($todo1);
        $this->assertEquals(1, $repository->count());

        $uid = $repository->nextIdentity();
        $todo2 = new Todo($uid, 2, false);

        $repository->add($todo2);
        $this->assertEquals(2, $repository->count());

        $todo3 = new Todo($todo1->uid(), 3, true);
        $repository->add($todo2);
        $this->assertEquals(2, $repository->count());

        $first = $repository->first();
        $this->assertEquals($first->uid(), $todo1->uid());
        $this->assertEquals($first->order(), $todo1->order());
        $this->assertEquals($first->isCompleted(), $todo1->isCompleted());
        $this->assertEquals($first->title(), $todo1->title());
    }

    public function testShouldRemoveTodo()
    {
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);

        $uid = $repository->nextIdentity();
        $todo1 = new Todo($uid, 1, true);
        $repository->add($todo1);

        $uid = $repository->nextIdentity();
        $todo2 = new Todo($uid, 2, false);

        $repository->add($todo2);

        $this->assertEquals(2, $repository->count());

        $repository->remove($todo1);
        $this->assertEquals(1, $repository->count());
        $this->assertfalse($repository->contains($todo1));
        $this->assertTrue($repository->contains($todo2));
    }

    public function testShouldGetAll()
    {
        $repository = new ZendTodoRepository([
            "driver"   => "Pdo_Sqlite",
            "database" => "/tmp/skeleton.sqlite3",
        ]);
        $this->assertEquals(0, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo($uid, 1, true);
        $repository->add($todo);
        $this->assertEquals(1, $repository->count());

        $uid = $repository->nextIdentity();
        $todo = new Todo($uid, 2, false);
        $repository->add($todo);

        $all = $repository->all();
        $this->assertEquals(2, count($all));
        $this->assertInstanceOf(Todo::class, $all[0]);
        $this->assertInstanceOf(Todo::class, $all[1]);
    }
}
