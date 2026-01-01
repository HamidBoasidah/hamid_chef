<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefVacation extends BaseModel
{
    use HasFactory;
    protected $table = 'chef_vacations';

    protected $fillable = [
        'chef_id',
        'date',
        'note',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
}
