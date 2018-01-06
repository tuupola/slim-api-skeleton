<?php

namespace Skeleton\Domain;

use Countable;

interface TodoRepository extends Countable
{
    public function nextIdentity(): string;
    public function get(string $uid): ?Todo;
    public function all(array $specification): array;
    public function add(Todo $todo): bool;
    public function remove(Todo $todo): bool;
}
