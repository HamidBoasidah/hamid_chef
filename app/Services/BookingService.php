<?php

namespace App\Services;

use App\Repositories\BookingRepository;

class BookingService
{
    protected BookingRepository $bookings;

    public function __construct(BookingRepository $bookings)
    {
        $this->bookings = $bookings;
    }

    public function all(array $with = [])
    {
        return $this->bookings->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->bookings->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->bookings->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->bookings->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->bookings->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->bookings->delete($id);
    }

    public function activate($id)
    {
        return $this->bookings->activate($id);
    }

    public function deactivate($id)
    {
        return $this->bookings->deactivate($id);
    }
}
