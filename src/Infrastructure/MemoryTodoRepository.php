<?php
declare(strict_types=1);

namespace Skeleton\Infrastructure;

use Skeleton\Application\Todo\TodoHydratorFactory;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;
use Tuupola\Base62;

use function Functional\map;

class MemoryTodoRepository implements TodoRepository
{
    private $todos = [];
    private $hydrator;

    public function __construct(array $config = [])
    {
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function nextIdentity(): string
    {
        return (new Base62)->encode(random_bytes(9));
    }

    public function get(string $uid): Todo
    {
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
        return end($this->todos);
    }

    public function add(Todo $todo): void
    {
        $this->todos[$todo->uid()] = $todo;
    }

    public function remove(Todo $todo): void
    {
        unset($this->todos[$todo->uid()]);
    }

    public function contains(Todo $todo): bool
    {
        return isset($this->todos[$todo->uid()]);
    }

    public function count(): int
    {
        return count($this->todos);
    }
}
