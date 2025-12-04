<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'avatar',
        'address',
        'phone_number',
        'whatsapp_number',
        'password',
        'facebook',
        'x_url',
        'linkedin',
        'instagram',
        'is_active',
        'user_type',
        'locale',
        'created_by',
        'updated_by',
    ];

    protected array $dontLog = ['password', 'remember_token'];

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }
    public function updatedUsers()
    {
        return $this->hasMany(User::class, 'updated_by');
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(Kyc::class, 'user_id');
    }

    public function chef(): HasOne
    {
        return $this->hasOne(Chef::class , 'user_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // default user type when creating from model
    protected $attributes = [
        'user_type' => 'customer',
    ];

    public function getNameAttribute()
    {
        $parts = array_filter([
            $this->first_name ?? null,
            $this->last_name ?? null,
        ], fn($p) => !is_null($p) && $p !== '');

        return $parts ? implode(' ', $parts) : null;
    }
}
