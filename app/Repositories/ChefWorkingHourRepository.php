<?php

namespace App\Repositories;

use App\Models\ChefWorkingHour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Repositories\Eloquent\BaseRepository;

class ChefWorkingHourRepository extends BaseRepository
{
    protected function model(): string
    {
        return ChefWorkingHour::class;
    }

    public function query(?array $with = null): Builder
    {
        return parent::query($with)->orderBy('day_of_week')->orderBy('start_time');
    }

    public function forChef(int $chefId, ?array $with = null): Builder
    {
        return $this->query($with)->where('chef_id', $chefId);
    }

    public function deleteByChef(int $chefId): int
    {
        return $this->forChef($chefId)->delete();
    }

    public function findForChefById(int $id, int $chefId)
    {
        return $this->forChef($chefId)->findOrFail($id);
    }

    public function getDayIntervals(int $chefId, int $dayOfWeek)
    {
        return $this->forChef($chefId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time']);
    }

    /**
     * Bulk insert records for a chef.
     * @param int $chefId
     * @param array<int,array{day_of_week:int,start_time:string,end_time:string,is_active?:bool}> $records
     */
    public function bulkInsert(int $chefId, array $records): void
    {
        $now = now();
        $payload = array_map(function ($r) use ($chefId, $now) {
            return [
                'chef_id' => $chefId,
                'day_of_week' => (int) $r['day_of_week'],
                'start_time' => $r['start_time'],
                'end_time' => $r['end_time'],
                'is_active' => $r['is_active'] ?? true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $records);

        if (!empty($payload)) {
            DB::table('chef_working_hours')->insert($payload);
        }
    }
}
