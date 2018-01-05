<?php

namespace Skeleton\Infrastructure;

use Skeleton\Application\TodoHydratorFactory;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;
use Tuupola\Base62;
use Zend\Db\Adapter\Adapter;
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

    public function get(string $uid): ?Todo
    {
        $rowset = $this->table->select(["uid" => $uid]);
        if (null === $row = $rowset->current()) {
            return null;
        }
        return $this->hydrator->hydrate((array) $row, new Todo);
    }

    public function query(array $specification = []): array
    {
        $rowset = $this->table->select($specification);
        return map($rowset, function ($row) {
            return $this->hydrator->hydrate((array) $row, new Todo);
        });
    }

    public function first(array $specification = []): Todo
    {
        $rowset = $this->table->select($specification);
        $row = $rowset->current();
        return $this->hydrator->hydrate((array) $row, new Todo);
    }

    public function save(Todo $todo)
    {
        return true;
    }

    public function saveAll(array $todos)
    {
        return true;
    }

    public function remove(Todo $todo)
    {
        return true;
    }

    public function removeAll(array $todos)
    {
        return true;
    }
}