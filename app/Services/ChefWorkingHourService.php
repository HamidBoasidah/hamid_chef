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
     * Return off-hour intervals (outside working hours) for a given day of week (0-6).
     * If $dayOfWeek is null, use current day.
     * Returns array of ['start_time' => 'HH:MM', 'end_time' => 'HH:MM'] representing off periods.
     */
    public function getOffHoursForCurrentChef(?int $dayOfWeek = null): array
    {
        $chefId = $this->currentChefId();
        $day = $dayOfWeek !== null ? (int) $dayOfWeek : (int) now()->dayOfWeek;

        $intervals = $this->repo->getDayIntervals($chefId, $day)->map(function ($r) {
            // normalize times to HH:MM (remove seconds if present)
            $start = explode(':', $r->start_time);
            $end = explode(':', $r->end_time);
            $s = sprintf('%02d:%02d', (int)$start[0], (int)($start[1] ?? 0));
            $e = sprintf('%02d:%02d', (int)$end[0], (int)($end[1] ?? 0));
            return ['start' => $s, 'end' => $e];
        })->toArray();

        // convert to minutes and sort
        $mins = array_map(function ($iv) {
            [$sh, $sm] = explode(':', $iv['start']);
            [$eh, $em] = explode(':', $iv['end']);
            return [ 'start' => (int)$sh * 60 + (int)$sm, 'end' => (int)$eh * 60 + (int)$em ];
        }, $intervals);

        usort($mins, fn($a, $b) => $a['start'] <=> $b['start']);

        // merge overlaps just in case
        $merged = [];
        foreach ($mins as $iv) {
            if (empty($merged)) { $merged[] = $iv; continue; }
            $last = &$merged[count($merged)-1];
            if ($iv['start'] <= $last['end']) {
                // overlap or contiguous
                $last['end'] = max($last['end'], $iv['end']);
            } else {
                $merged[] = $iv;
            }
        }

        $off = [];
        $dayStart = 0;
        $dayEnd = 24 * 60;

        if (empty($merged)) {
            // whole day off
            return [['start_time' => '00:00', 'end_time' => '24:00']];
        }

        // gap before first
        if ($merged[0]['start'] > $dayStart) {
            $off[] = ['start_time' => $this->minutesToTime($dayStart), 'end_time' => $this->minutesToTime($merged[0]['start'])];
        }

        // gaps between
        for ($i = 0; $i < count($merged) - 1; $i++) {
            $cur = $merged[$i];
            $next = $merged[$i+1];
            if ($next['start'] > $cur['end']) {
                $off[] = ['start_time' => $this->minutesToTime($cur['end']), 'end_time' => $this->minutesToTime($next['start'])];
            }
        }

        // gap after last
        $last = $merged[count($merged)-1];
        if ($last['end'] < $dayEnd) {
            $off[] = ['start_time' => $this->minutesToTime($last['end']), 'end_time' => $this->minutesToTime($dayEnd)];
        }

        return $off;
    }

    protected function minutesToTime(int $m): string
    {
        if ($m === 24*60) return '24:00';
        $h = intdiv($m, 60);
        $mm = $m % 60;
        return sprintf('%02d:%02d', $h, $mm);
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
