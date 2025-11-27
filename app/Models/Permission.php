<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{

    // Use admin guard by default to match our ACL config
    protected $guard_name = 'admin';

    protected $fillable = ['name', 'guard_name'];
}
