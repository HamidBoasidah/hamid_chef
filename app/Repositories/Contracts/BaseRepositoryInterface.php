<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function query(?array $with = null);

    public function all(?array $with = null);

    public function paginate(int $perPage = 10, ?array $with = null);

    public function find(int|string $id, ?array $with = null);

    public function findOrFail(int|string $id, ?array $with = null);

    public function create(array $attributes);

    public function update(int|string $id, array $attributes);

    public function delete(int|string $id): bool;

    public function activate(int|string $id);

    public function deactivate(int|string $id);
}
