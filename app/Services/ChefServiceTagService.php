<?php

namespace App\Services;

use App\Repositories\ChefServiceTagRepository;

class ChefServiceTagService
{
    protected ChefServiceTagRepository $serviceTags;

    public function __construct(ChefServiceTagRepository $serviceTags)
    {
        $this->serviceTags = $serviceTags;
    }

    public function all(array $with = [])
    {
        return $this->serviceTags->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->serviceTags->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->serviceTags->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->serviceTags->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->serviceTags->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->serviceTags->delete($id);
    }

    public function activate($id)
    {
        return $this->serviceTags->activate($id);
    }

    public function deactivate($id)
    {
        return $this->serviceTags->deactivate($id);
    }
}
