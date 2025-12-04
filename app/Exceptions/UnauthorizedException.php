<?php

namespace App\Exceptions;

class UnauthorizedException extends StationManagementException
{
    public function __construct(string $message = 'غير مصرح لك بالوصول لهذا المورد')
    {
        parent::__construct($message, 401, 'UNAUTHORIZED');
    }
} 