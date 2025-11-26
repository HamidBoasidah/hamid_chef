<?php

namespace App\Services;

use App\Repositories\KycRepository;

class KycService
{
    protected KycRepository $kycs;

    public function __construct(KycRepository $kycs)
    {
        $this->kycs = $kycs;
    }

    public function all(array $with = [])
    {
        return $this->kycs->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->kycs->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->kycs->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->kycs->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->kycs->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->kycs->delete($id);
    }

    public function activate($id)
    {
        return $this->kycs->activate($id);
    }

    public function deactivate($id)
    {
        return $this->kycs->deactivate($id);
    }
}
