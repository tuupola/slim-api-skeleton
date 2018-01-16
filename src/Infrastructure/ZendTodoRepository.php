<?php
declare(strict_types=1);

namespace Skeleton\Infrastructure;

use Skeleton\Application\Todo\TodoHydratorFactory;
use Skeleton\Application\Todo\TodoNotFoundException;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;
use Tuupola\Base62;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Where;
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

    public function nextIdentity(): string
    {
        return (new Base62)->encode(random_bytes(9));
    }

    public function get(string $uid): Todo
    {
        $rowset = $this->table->select(["uid" => $uid]);
        if (null === $row = $rowset->current()) {
            throw new TodoNotFoundException;
        }
        return $this->hydrator->hydrate((array) $row, new Todo);
    }

    public function all(array $specification = []): array
    {
        $rowset = $this->table->select($specification);
        return map($rowset, function ($row) {
            return $this->hydrator->hydrate((array) $row, new Todo);
        });
    }

    public function first(array $specification = []): Todo
    {
        $rowset = $this->table->select($specification);
        if (null === $row = $rowset->current()) {
            throw new TodoNotFoundException;
        }
        return $this->hydrator->hydrate((array) $row, new Todo);
    }

    public function add(Todo $todo): void
    {
        $data = $this->hydrator->extract($todo);
        if ($this->contains($todo)) {
            $where = ["uid" => $todo->uid()];
            $this->table->update($data, $where);
        } else {
            $this->table->insert($data);
        }
    }

    public function remove(Todo $todo): void
    {
        $where["uid"] = $todo->uid();
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
        return 0;
    }
}
