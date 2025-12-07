<?php

namespace App\Exceptions;

class ProductCodeException extends ApplicationException
{
    public function __construct(string $message = 'رمز المنتج غير صحيح')
    {
        parent::__construct($message, 422, 'PRODUCT_CODE_ERROR');
    }
} 