<?php
declare(strict_types=1);

namespace Skeleton\Infrastructure;

use ReflectionClass;
use Skeleton\Application\Todo\TodoHydratorFactory;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoUid;
use Skeleton\Domain\TodoRepository;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\Feature\MetadataFeature;
use Zend\Db\TableGateway\Feature\FeatureSet;
use function Functional\map;

class ZendTodoRepository implements TodoRepository
{
    private $table;
    private $hydrator;

    public function __construct(array $config)
    {
        $adapter = new Adapter($config);
        $this->table = new TableGateway("todos", $adapter);
        $this->hydrator = (new TodoHydratorFactory)->create();
    }

    public function nextIdentity(): TodoUid
    {
        return new TodoUid;
    }

    public function get(TodoUid $uid): Todo
    {
        $rowset = $this->table->select(["uid" => (string) $uid]);
        if (null === $row = $rowset->current()) {
            throw new TodoNotFoundException;
        }
        return $this->hydrator->hydrate(
            (array) $row,
            (new ReflectionClass(Todo::class))->newInstanceWithoutConstructor()
        );
    }

    public function all(array $specification = []): array
    {
        $rowset = $this->table->select($specification);
        return map($rowset, function ($row) {
            return $this->hydrator->hydrate(
                (array) $row,
                (new ReflectionClass(Todo::class))->newInstanceWithoutConstructor()
            );
        });
    }

    public function first(): Todo
    {
        $rowset = $this->table->select(function (Select $select) {
            $select->order("updated_at ASC")->limit(1);
        });

        if (null === $row = $rowset->current()) {
            throw new TodoNotFoundException;
        }
        return $this->hydrator->hydrate(
            (array) $row,
            (new ReflectionClass(Todo::class))->newInstanceWithoutConstructor()
        );
    }

    public function last(): Todo
    {
        $rowset = $this->table->select(function (Select $select) {
            $select->order("updated_at DESC")->limit(1);
        });

        if (null === $row = $rowset->current()) {
            throw new TodoNotFoundException;
        }
        return $this->hydrator->hydrate(
            (array) $row,
            (new ReflectionClass(Todo::class))->newInstanceWithoutConstructor()
        );
    }

    public function add(Todo $todo): void
    {
        $data = $this->hydrator->extract($todo);
        if ($this->contains($todo)) {
            $where["uid"] = (string) $todo->uid();
            $this->table->update($data, $where);
        } else {
            $this->table->insert($data);
        }
    }

    public function remove(Todo $todo): void
    {
        $where["uid"] = (string) $todo->uid();
        $this->table->delete($where);
    }

    public function contains(Todo $todo): bool
    {
        try {
            $this->get($todo->uid());
        } catch (TodoNotFoundException $exception) {
            return false;
        }
        return true;
    }

    public function count(): int
    {
        $rowset = $this->table->select();
        return count($rowset);
    }
}
