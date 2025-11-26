<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    protected TagRepository $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    public function all(array $with = [])
    {
        return $this->tags->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->tags->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->tags->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->tags->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->tags->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->tags->delete($id);
    }

    public function activate($id)
    {
        return $this->tags->activate($id);
    }

    public function deactivate($id)
    {
        return $this->tags->deactivate($id);
    }
}
