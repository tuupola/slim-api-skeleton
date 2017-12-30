<?php

namespace Skeleton\Infrastructure;

#use GeneratedHydrator\Configuration as HydratorConfiguration;
use Skeleton\Domain\Todo;
use Skeleton\Domain\TodoRepository;
use Tuupola\Base62;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\Feature\MetadataFeature;
use Zend\Db\TableGateway\Feature\FeatureSet;
use Zend\Hydrator\Reflection as ReflectionHydrator;
#use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\NamingStrategy\MapNamingStrategy;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Hydrator\Strategy\BooleanStrategy;

class ZendTodoRepository implements TodoRepository
{
    private $table;
    private $hydrator;

    public function __construct(array $config)
    {
        $adapter = new Adapter($config);
        $this->table = new TableGateway("todos", $adapter);

        $this->hydrator = new ReflectionHydrator;
        $this->hydrator->setNamingStrategy(new MapNamingStrategy([
            "created_at" => "createdAt",
            "updated_at" => "updatedAt",
        ]));
        $this->hydrator->addStrategy(
            "createdAt",
            new DateTimeFormatterStrategy("Y-m-d H:i:s")
        );
        $this->hydrator->addStrategy(
            "updatedAt",
            new DateTimeFormatterStrategy("Y-m-d H:i:s")
        );
        $this->hydrator->addStrategy(
            "completed",
            new BooleanStrategy(0, 1)
        );
    }

    public function nextUid(): string
    {
        return (new Base62)->encode(random_bytes(9));
    }

    public function get(string $uid): Todo
    {
        $rowset = $this->table->select(["uid" => $uid]);
        if (null === $row = $rowset->current()) {
            /* Throw something. */
        }
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

    public function delete(Todo $todo)
    {
        return true;
    }

    public function deleteAll(array $todos)
    {
        return true;
    }
}
