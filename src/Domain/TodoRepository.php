<?php

namespace Skeleton\Domain;

interface TodoRepository
{
    public function nextIdentity(): string;
    public function get(string $uid): ?Todo;
    public function query(array $specification): array;
    public function save(Todo $todo);
    public function remove(Todo $todo);
}
