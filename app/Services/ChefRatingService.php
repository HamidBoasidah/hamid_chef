<?php

namespace App\Services;

use App\Repositories\ChefRatingRepository;

class ChefRatingService
{
    protected ChefRatingRepository $ratings;

    public function __construct(ChefRatingRepository $ratings)
    {
        $this->ratings = $ratings;
    }

    public function all(array $with = [])
    {
        return $this->ratings->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->ratings->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->ratings->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->ratings->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->ratings->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->ratings->delete($id);
    }

    public function activate($id)
    {
        return $this->ratings->activate($id);
    }

    public function deactivate($id)
    {
        return $this->ratings->deactivate($id);
    }
}
