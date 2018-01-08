<?php
declare(strict_types=1);

namespace Skeleton\Domain;

use Countable;

interface TodoRepository extends Countable
{
    public function nextIdentity(): string;
    public function get(string $uid): Todo;
    public function all(array $specification): array;
    public function add(Todo $todo): void;
    public function remove(Todo $todo): void;
}
