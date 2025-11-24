<?php

namespace App\DTOs;

use App\Models\User;
 
class UserDTO extends BaseDTO
{
    public $id;
    public $name;
    public $email;
    public $address;
    public $phone_number;
    public $whatsapp_number;
    public $is_active;
    public $attachment;
    public $role;
    public $role_id;

    public function __construct($id, $name, $email, $address, $phone_number, $whatsapp_number, $is_active, $attachment, $role, $role_id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->address = $address;
        $this->phone_number = $phone_number;
        $this->whatsapp_number = $whatsapp_number;
        $this->is_active = $is_active;
        $this->attachment = $attachment;
        $this->role = $role;
        $this->role_id = $role_id;
    }

    public static function fromModel(User $user): self
    {
        $user->loadMissing(['roles']);
        $role = $user->roles->First();
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->address,
            $user->phone_number,
            $user->whatsapp_number,
            $user->is_active,
            $user->attachment,
            $role ? [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->getTranslations('display_name'),
            ] : null,
            $role?->id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'whatsapp_number' => $this->whatsapp_number,
            'is_active' => $this->is_active,
            'attachment' => $this->attachment,
            'role' => $this->role,
            'role_id' => $this->role_id,
        ];
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'is_active' => $this->is_active,
            'attachment' => $this->attachment,
            'role' => $this->role,
        ];
    }
}
