<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function all(array $with = [])
    {
        return $this->categories->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->categories->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->categories->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->categories->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->categories->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->categories->delete($id);
    }

    public function activate($id)
    {
        return $this->categories->activate($id);
    }

    public function deactivate($id)
    {
        return $this->categories->deactivate($id);
    }
}
