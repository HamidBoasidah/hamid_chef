<?php

namespace App\Services;

use App\Repositories\ChefCategoryRepository;

class ChefCategoryService
{
    protected ChefCategoryRepository $chefCategories;

    public function __construct(ChefCategoryRepository $chefCategories)
    {
        $this->chefCategories = $chefCategories;
    }

    public function all(array $with = [])
    {
        return $this->chefCategories->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->chefCategories->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->chefCategories->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->chefCategories->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->chefCategories->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->chefCategories->delete($id);
    }

    public function activate($id)
    {
        return $this->chefCategories->activate($id);
    }

    public function deactivate($id)
    {
        return $this->chefCategories->deactivate($id);
    }
}
