<?php

namespace App\Services;

use App\Repositories\ChefWithdrawalRequestRepository;

class ChefWithdrawalRequestService
{
    protected ChefWithdrawalRequestRepository $withdrawals;

    public function __construct(ChefWithdrawalRequestRepository $withdrawals)
    {
        $this->withdrawals = $withdrawals;
    }

    public function all(array $with = [])
    {
        return $this->withdrawals->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->withdrawals->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->withdrawals->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->withdrawals->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->withdrawals->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->withdrawals->delete($id);
    }

    public function activate($id)
    {
        return $this->withdrawals->activate($id);
    }

    public function deactivate($id)
    {
        return $this->withdrawals->deactivate($id);
    }
}
