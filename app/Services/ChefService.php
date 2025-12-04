<?php

namespace App\Services;

use App\Repositories\ChefRepository;

class ChefService
{
    protected ChefRepository $chefs;

    public function __construct(ChefRepository $chefs)
    {
        $this->chefs = $chefs;
    }

    public function all(array $with = [])
    {
        return $this->chefs->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->chefs->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->chefs->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->chefs->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->chefs->update($id, $attributes);
    }

    /*public function createOrUpdate(array $attributes)
    {
        return $this->chefs->createOrUpdate($attributes);
    }*/


    public function delete($id)
    {
        return $this->chefs->delete($id);
    }

    public function activate($id)
    {
        return $this->chefs->activate($id);
    }

    public function deactivate($id)
    {
        return $this->chefs->deactivate($id);
    }
}
