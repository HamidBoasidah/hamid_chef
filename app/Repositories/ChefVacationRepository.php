<?php

namespace App\Repositories;

use App\Models\ChefVacation;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Eloquent\BaseRepository;

class ChefVacationRepository extends BaseRepository
{
    protected function model(): string
    {
        return ChefVacation::class;
    }

    public function query(?array $with = null): Builder
    {
        return parent::query($with)->orderBy('date');
    }

    public function forChef(int $chefId, ?array $with = null): Builder
    {
        return $this->query($with)->where('chef_id', $chefId);
    }

    public function findForChefById(int $id, int $chefId)
    {
        return $this->forChef($chefId)->findOrFail($id);
    }

    public function existsDate(int $chefId, string $date): bool
    {
        return $this->forChef($chefId)->where('date', $date)->exists();
    }
}
