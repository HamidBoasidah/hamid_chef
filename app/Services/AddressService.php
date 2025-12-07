<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddressService
{
    protected AddressRepository $addresses;

    public function __construct(AddressRepository $addresses)
    {
        $this->addresses = $addresses;
    }

     public function all(array $with = [])
    {
        return $this->addresses->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->addresses->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->addresses->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        // Ensure the address is associated with the currently authenticated user
        if (empty($attributes['user_id'])) {
            $attributes['user_id'] = Auth::id();
        }

        return $this->addresses->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $chef = $this->addresses->findOrFail($id);
        return $this->addresses->update($chef, $attributes);
    }

    public function delete($id)
    {
        return $this->addresses->delete($id);
    }

    public function activate($id)
    {
        return $this->addresses->activate($id);
    }

    public function deactivate($id)
    {
        return $this->addresses->deactivate($id);
    }
}
