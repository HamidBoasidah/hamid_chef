<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Repositories\ChefVacationRepository;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChefVacationService
{
    public function __construct(protected ChefVacationRepository $repo, protected BookingRepository $bookings)
    {
    }

    protected function currentChefId(): int
    {
        $chefId = optional(Auth::user()->chef)->id;
        if (!$chefId) {
            throw ValidationException::withMessages([
                'chef' => ['يجب إنشاء ملف الطاهي قبل إدارة الإجازات.']
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
     * Get vacations for current chef filtered by month (YYYY-MM). Defaults to current month.
     */
    public function getForCurrentChefByMonth(?string $month)
    {
        $chefId = $this->currentChefId();
        // DEBUG: Log received month parameter
        \Log::info('ChefVacationService::getForCurrentChefByMonth', ['chef_id' => $chefId, 'received_month' => $month]);
        $monthDate = $month ? Carbon::createFromFormat('Y-m', $month) : Carbon::now();
        $start = $monthDate->copy()->startOfMonth();
        $end = $monthDate->copy()->endOfMonth();

        return $this->repo->forChef($chefId)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();
    }

    public function showForCurrentChef(int $id)
    {
        $chefId = $this->currentChefId();
        return $this->repo->findForChefById($id, $chefId);
    }

    public function createForCurrentChef(array $data)
    {
        $chefId = $this->currentChefId();
        $date = $data['date'];
        // DEBUG: Log received data
        \Log::info('ChefVacationService::createForCurrentChef', ['chef_id' => $chefId, 'received_data' => $data, 'date' => $date]);
        // Prevent creating a vacation on a day that has active bookings
        $hasActiveBooking = $this->bookings->hasActiveBookingsOnDate($chefId, Carbon::parse($date));
        \Log::info('ChefVacationService::createForCurrentChef - Booking check', ['has_active_booking' => $hasActiveBooking, 'date' => $date]);
        if ($hasActiveBooking) {
            throw ValidationException::withMessages([
                'date' => ['لا يمكن إضافة إجازة لوجود حجز نشط في نفس اليوم']
            ]);
        }
        if ($this->repo->existsDate($chefId, $date)) {
            throw ValidationException::withMessages([
                'date' => ['تاريخ الإجازة موجود بالفعل']
            ]);
        }
        return $this->repo->create([
            'chef_id' => $chefId,
            'date' => $date,
            'note' => $data['note'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateForCurrentChef(int $id, array $data)
    {
        $chefId = $this->currentChefId();
        $record = $this->repo->findForChefById($id, $chefId);

        $attributes = [];
        if (array_key_exists('date', $data)) {
            $newDate = $data['date'];
            if ($newDate !== $record->date && $this->repo->existsDate($chefId, $newDate)) {
                throw ValidationException::withMessages([
                    'date' => ['تاريخ الإجازة موجود بالفعل']
                ]);
            }
            // Prevent moving vacation to a date that has active bookings
            if ($this->bookings->hasActiveBookingsOnDate($chefId, Carbon::parse($newDate))) {
                throw ValidationException::withMessages([
                    'date' => ['لا يمكن نقل الإجازة إلى يوم لديه حجز نشط']
                ]);
            }
            $attributes['date'] = $newDate;
        }
        if (array_key_exists('note', $data)) {
            $attributes['note'] = $data['note'];
        }
        if (array_key_exists('is_active', $data)) {
            $attributes['is_active'] = (bool) $data['is_active'];
        }

        return $this->repo->updateModel($record, $attributes);
    }

    public function deleteForCurrentChef(int $id): bool
    {
        $chefId = $this->currentChefId();
        $this->repo->findForChefById($id, $chefId);
        return $this->repo->delete($id);
    }
}
