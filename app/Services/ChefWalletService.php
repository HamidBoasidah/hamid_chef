<?php

namespace App\Services;

use App\Repositories\ChefWalletRepository;

class ChefWalletService
{
    protected ChefWalletRepository $wallets;

    public function __construct(ChefWalletRepository $wallets)
    {
        $this->wallets = $wallets;
    }

    public function all(array $with = [])
    {
        return $this->wallets->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->wallets->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->wallets->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->wallets->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $wallet = $this->wallets->findOrFail($id);
        return $this->wallets->update($wallet, $attributes);
    }

    public function delete($id)
    {
        return $this->wallets->delete($id);
    }

    public function activate($id)
    {
        return $this->wallets->activate($id);
    }

    public function deactivate($id)
    {
        return $this->wallets->deactivate($id);
    }
}
