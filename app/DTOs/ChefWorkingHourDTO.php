<?php

namespace App\DTOs;

use App\Models\ChefWorkingHour;

class ChefWorkingHourDTO extends BaseDTO
{
    public $id;
    public $day_of_week;
    public $start_time;
    public $end_time;

    public function __construct($id, $day_of_week, $start_time, $end_time)
    {
        $this->id = $id;
        $this->day_of_week = $day_of_week;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public static function fromModel(ChefWorkingHour $m): self
    {
        return new self(
            $m->id,
            $m->day_of_week,
            $m->start_time,
            $m->end_time,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }
}
