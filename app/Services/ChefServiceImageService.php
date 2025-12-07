<?php

namespace App\Services;

use App\Repositories\ChefServiceImageRepository;

class ChefServiceImageService
{
    protected ChefServiceImageRepository $images;

    public function __construct(ChefServiceImageRepository $images)
    {
        $this->images = $images;
    }

    public function all(array $with = [])
    {
        return $this->images->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->images->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->images->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->images->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $image = $this->images->findOrFail($id);
        return $this->images->update($image, $attributes);
    }

    public function delete($id)
    {
        return $this->images->delete($id);
    }

    public function activate($id)
    {
        return $this->images->activate($id);
    }

    public function deactivate($id)
    {
        return $this->images->deactivate($id);
    }
}
