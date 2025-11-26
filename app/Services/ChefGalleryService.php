<?php

namespace App\Services;

use App\Repositories\ChefGalleryRepository;

class ChefGalleryService
{
    protected ChefGalleryRepository $galleries;

    public function __construct(ChefGalleryRepository $galleries)
    {
        $this->galleries = $galleries;
    }

    public function all(array $with = [])
    {
        return $this->galleries->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->galleries->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->galleries->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->galleries->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->galleries->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->galleries->delete($id);
    }

    public function activate($id)
    {
        return $this->galleries->activate($id);
    }

    public function deactivate($id)
    {
        return $this->galleries->deactivate($id);
    }
}
