<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Expose an Eloquent query builder for controllers that need to apply
     * additional constraints or filters before pagination.
     */
    public function query(?array $with = null): Builder
    {
        return $this->categories->query($with);
    }

    public function find($id, array $with = [])
    {
        return $this->categories->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        // Ensure slug is generated from name when creating
        if (empty($attributes['slug']) && ! empty($attributes['name'])) {
            $attributes['slug'] = $this->makeUniqueSlug($attributes['name']);
        }

        return $this->categories->create($attributes);
    }

    public function update($id, array $attributes)
    {
        // Generate slug from name on update if name provided
        if (! empty($attributes['name'])) {
            $attributes['slug'] = $this->makeUniqueSlug($attributes['name'], $id);
        }

        return $this->categories->update($id, $attributes);
    }

    /**
     * Generate a unique slug for the given name.
     * If $ignoreId is provided, ignore that record when checking uniqueness (useful on update).
     */
    protected function makeUniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Category::where('slug', $slug)->when($ignoreId, function ($q) use ($ignoreId) {
            $q->where('id', '!=', $ignoreId);
        })->exists()) {
            $slug = $base.'-'.++$i;
        }

        return $slug;
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
