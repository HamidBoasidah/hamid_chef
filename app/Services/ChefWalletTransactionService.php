<?php

namespace App\Services;

use App\Repositories\ChefWalletTransactionRepository;

class ChefWalletTransactionService
{
    protected ChefWalletTransactionRepository $transactions;

    public function __construct(ChefWalletTransactionRepository $transactions)
    {
        $this->transactions = $transactions;
    }

    public function all(array $with = [])
    {
        return $this->transactions->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->transactions->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->transactions->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->transactions->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $transaction = $this->transactions->findOrFail($id);
        return $this->transactions->update($transaction, $attributes);
    }

    public function delete($id)
    {
        return $this->transactions->delete($id);
    }

    public function activate($id)
    {
        return $this->transactions->activate($id);
    }

    public function deactivate($id)
    {
        return $this->transactions->deactivate($id);
    }
}
