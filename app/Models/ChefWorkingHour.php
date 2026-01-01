<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefWorkingHour extends BaseModel
{
    protected $table = 'chef_working_hours';

    protected $fillable = [
        'chef_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_active' => 'boolean',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
}
