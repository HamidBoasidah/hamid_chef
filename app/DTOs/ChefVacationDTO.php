<?php

namespace App\DTOs;

use App\Models\ChefVacation;

class ChefVacationDTO extends BaseDTO
{
    public $id;
    public $date;
    public $note;
    public $is_active;

    public function __construct($id, $date, $note, $is_active)
    {
        $this->id = $id;
        $this->date = $date;
        $this->note = $note;
        $this->is_active = (bool) $is_active;
    }

    public static function fromModel(ChefVacation $m): self
    {
        return new self(
            $m->id,
            $m->date?->format('Y-m-d'),
            $m->note,
            $m->is_active,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'note' => $this->note,
            'is_active' => $this->is_active,
        ];
    }
}
