<?php

namespace Skeleton\Domain;

interface TodoRepository
{
    public function nextUid(): string;
    public function get(string $uid): Todo;
    public function save(Todo $todo);
    public function saveAll(array $todos);
    public function delete(Todo $todo);
    public function deleteAll(array $todos);
}
