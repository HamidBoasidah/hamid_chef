<?php

namespace App\Exceptions;

class ValidationRuleException extends StationManagementException
{
    public function __construct(string $message = 'قاعدة التحقق غير صحيحة')
    {
        parent::__construct($message, 422, 'VALIDATION_RULE_ERROR');
    }
} 