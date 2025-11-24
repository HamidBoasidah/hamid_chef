<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        $user = parent::create($data); // استدعاء دالة الإنشاء من الأب
        $role = Role::find($data['role_id']);
        if ($role) {
            $user->assignRole($role->name);
        }
        return $user;
    }

    public function update($id, array $data)
    {
        $user = parent::update($id, $data);
        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $user->syncRoles([$role->name]);
            }
        }
        return $user;
    }
}
