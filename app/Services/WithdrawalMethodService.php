<?php

namespace App\Services;

use App\Repositories\WithdrawalMethodRepository;

class WithdrawalMethodService
{
    protected WithdrawalMethodRepository $methods;

    public function __construct(WithdrawalMethodRepository $methods)
    {
        $this->methods = $methods;
    }

    public function all(array $with = [])
    {
        return $this->methods->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->methods->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->methods->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->methods->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $method = $this->methods->findOrFail($id);
        return $this->methods->update($method, $attributes);
    }

    public function delete($id)
    {
        return $this->methods->delete($id);
    }

    public function activate($id)
    {
        return $this->methods->activate($id);
    }

    public function deactivate($id)
    {
        return $this->methods->deactivate($id);
    }
}
