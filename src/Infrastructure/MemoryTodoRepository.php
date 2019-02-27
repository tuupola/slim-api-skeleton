<?php
declare(strict_types=1);

namespace Skeleton\Infrastructure;

use Skeleton\Application\Todo\TodoHydratorFactory;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;
use Skeleton\Domain\TodoRepository;

use function Functional\map;

class MemoryTodoRepository implements TodoRepository
{
    private $todos = [];
    private $hydrator;

    public function __construct(array $config = [])
    {
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function nextIdentity(): TodoUid
    {
        return new TodoUid;
    }

    public function get(TodoUid $uid): Todo
    {
        $uid = (string) $uid;

        if (isset($this->todos[$uid])) {
            return $this->todos[$uid];
        }
        throw new TodoNotFoundException;
    }

    public function all(array $specification = []): array
    {
        return array_values($this->todos);
    }

    public function first(array $specification = []): Todo
    {
        if (empty($this->todos)) {
            throw new TodoNotFoundException;
        }
        return reset($this->todos);
    }

    public function add(Todo $todo): void
    {
        $uid = (string) $todo->uid();
        $this->todos[$uid] = $todo;
    }

    public function remove(Todo $todo): void
    {
        $uid = (string) $todo->uid();
        unset($this->todos[$uid]);
    }

    public function contains(Todo $todo): bool
    {
        $uid = (string) $todo->uid();
        return isset($this->todos[$uid]);
    }

    public function count(): int
    {
        return count($this->todos);
    }
}
