<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Models\ChefWorkingHour;
use App\Repositories\ChefWorkingHourRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChefWorkingHourService
{
    public function __construct(protected ChefWorkingHourRepository $repo)
    {
    }

    protected function currentChefId(): int
    {
        $chefId = optional(Auth::user()->chef)->id;
        if (!$chefId) {
            throw ValidationException::withMessages([
                'chef' => ['يجب إنشاء ملف الطاهي قبل إدارة جدول العمل.']
            ]);
        }
        return (int) $chefId;
    }

    public function getForCurrentChef()
    {
        $chefId = $this->currentChefId();
        return $this->repo->forChef($chefId)->get();
    }

    /**
     * Replace the weekly schedule for current chef.
     * @param array $schedule e.g. [ ['day_of_week'=>1,'ranges'=>[['start_time'=>'09:00','end_time'=>'12:00']]] ]
     */
    public function replaceForCurrentChef(array $schedule)
    {
        $chefId = $this->currentChefId();

        // Flatten schedule to records
        $records = [];
        foreach ($schedule as $day) {
            $dayOfWeek = (int) ($day['day_of_week'] ?? -1);
            $ranges = $day['ranges'] ?? [];
            foreach ($ranges as $r) {
                $records[] = [
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $r['start_time'],
                    'end_time' => $r['end_time'],
                ];
            }
        }

        DB::transaction(function () use ($chefId, $records) {
            $this->repo->deleteByChef($chefId);
            $this->repo->bulkInsert($chefId, $records);
        });

        return $this->repo->forChef($chefId)->get();
    }

    /** Create single working hour interval for current chef */
    public function createForCurrentChef(array $data)
    {
        $chefId = $this->currentChefId();
        $day = (int) $data['day_of_week'];
        $start = $data['start_time'];
        $end = $data['end_time'];

        // Check overlaps on the same day
        $intervals = $this->repo->getDayIntervals($chefId, $day);
        foreach ($intervals as $iv) {
            if ($start < $iv->end_time && $end > $iv->start_time) {
                throw ValidationException::withMessages([
                    'ranges' => ['الفترة الزمنية تتقاطع مع فترة موجودة لنفس اليوم']
                ]);
            }
        }

        return $this->repo->create([
            'chef_id' => $chefId,
            'day_of_week' => $day,
            'start_time' => $start,
            'end_time' => $end,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /** Update single working hour interval for current chef */
    public function updateForCurrentChef(int $id, array $data)
    {
        $chefId = $this->currentChefId();
        $record = $this->repo->findForChefById($id, $chefId);

        $day = (int) ($data['day_of_week'] ?? $record->day_of_week);
        $start = $data['start_time'] ?? $record->start_time;
        $end = $data['end_time'] ?? $record->end_time;

        // Check overlaps excluding current record
        $intervals = $this->repo->getDayIntervals($chefId, $day);
        foreach ($intervals as $iv) {
            if ($iv->id === $record->id) continue;
            if ($start < $iv->end_time && $end > $iv->start_time) {
                throw ValidationException::withMessages([
                    'ranges' => ['الفترة الزمنية تتقاطع مع فترة موجودة لنفس اليوم']
                ]);
            }
        }

        $attributes = [
            'day_of_week' => $day,
            'start_time' => $start,
            'end_time' => $end,
        ];
        if (array_key_exists('is_active', $data)) {
            $attributes['is_active'] = (bool) $data['is_active'];
        }

        return $this->repo->updateModel($record, $attributes);
    }

    /** Delete single working hour interval for current chef */
    public function deleteForCurrentChef(int $id): bool
    {
        $chefId = $this->currentChefId();
        $this->repo->findForChefById($id, $chefId); // ensures ownership
        return $this->repo->delete($id);
    }
}
