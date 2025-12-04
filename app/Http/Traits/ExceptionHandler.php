<?php

namespace App\Http\Traits;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\BusinessLogicException;
use Illuminate\Database\Eloquent\Model;

trait ExceptionHandler
{
    /**
     * Throw not found exception
     */
    protected function throwNotFoundException(string $message = 'المورد المطلوب غير موجود'): void
    {
        throw new NotFoundException($message);
    }

    /**
     * Throw unauthorized exception
     */
    protected function throwUnauthorizedException(string $message = 'غير مصرح لك بالوصول لهذا المورد'): void
    {
        throw new UnauthorizedException($message);
    }

    /**
     * Throw forbidden exception
     */
    protected function throwForbiddenException(string $message = 'ممنوع الوصول لهذا المورد'): void
    {
        throw new ForbiddenException($message);
    }

    /**
     * Throw business logic exception
     */
    protected function throwBusinessLogicException(string $message = 'خطأ في منطق الأعمال'): void
    {
        throw new BusinessLogicException($message);
    }

    /**
     * Check if model exists or throw not found exception
     */
    protected function findOrFail(Model $model, string $message = null): Model
    {
        if (!$model) {
            throw new NotFoundException($message ?? 'المورد المطلوب غير موجود');
        }

        return $model;
    }

    /**
     * Validate business logic
     */
    protected function validateBusinessLogic(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new BusinessLogicException($message);
        }
    }
} 