<?php

namespace App\Exceptions;

class DataIntegrityException extends StationManagementException
{
    public function __construct(string $message = 'خطأ في سلامة البيانات')
    {
        parent::__construct($message, 422, 'DATA_INTEGRITY_ERROR');
    }
} 