<?php

namespace App\Exceptions;

class ResourceInUseException extends StationManagementException
{
    public function __construct(string $message = 'المورد مستخدم ولا يمكن حذفه')
    {
        parent::__construct($message, 409, 'RESOURCE_IN_USE');
    }
} 