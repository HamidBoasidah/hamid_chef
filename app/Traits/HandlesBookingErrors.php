<?php

namespace App\Traits;

use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesBookingErrors
{
    /**
     * Handle booking conflict errors
     */
    protected function handleConflictError(
        string $message,
        array $conflictingBookings = [],
        string $errorType = 'booking_conflict'
    ): JsonResponse {
        Log::warning('Booking conflict detected', [
            'message' => $message,
            'conflicting_bookings' => $conflictingBookings,
            'error_type' => $errorType,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => $errorType,
            'errors' => [$message],
            'conflicting_bookings' => $conflictingBookings
        ], 409);
    }

    /**
     * Handle validation errors
     */
    protected function handleValidationError(
        string $message,
        array $errors = [],
        string $errorType = 'validation_error'
    ): JsonResponse {
        Log::warning('Booking validation failed', [
            'message' => $message,
            'errors' => $errors,
            'error_type' => $errorType,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => $errorType,
            'errors' => !empty($errors) ? $errors : [$message]
        ], 422);
    }

    /**
     * Handle authentication errors
     */
    protected function handleAuthError(string $message = 'Unauthorized access'): JsonResponse
    {
        Log::warning('Booking authentication failed', [
            'message' => $message,
            'user_id' => auth()->id(),
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => 'authentication_error',
            'errors' => [$message]
        ], 401);
    }

    /**
     * Handle authorization errors
     */
    protected function handleAuthorizationError(string $message = 'Access forbidden'): JsonResponse
    {
        Log::warning('Booking authorization failed', [
            'message' => $message,
            'user_id' => auth()->id(),
            'ip' => request()->ip()
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => 'authorization_error',
            'errors' => [$message]
        ], 403);
    }

    /**
     * Handle general booking errors
     */
    protected function handleBookingError(
        \Exception $exception,
        string $defaultMessage = 'An error occurred while processing the booking'
    ): JsonResponse {
        $statusCode = 500;
        $errorType = 'system_error';
        $message = $defaultMessage;

        // Handle specific exception types
        if ($exception instanceof BookingConflictException) {
            $statusCode = 409;
            $errorType = $exception->getErrorType();
            $message = $exception->getMessage();
            
            return $this->handleConflictError(
                $message,
                $exception->getConflictingBookings(),
                $errorType
            );
        }

        if ($exception instanceof BookingValidationException) {
            $statusCode = 422;
            $errorType = $exception->getErrorType();
            $message = $exception->getMessage();
            
            return $this->handleValidationError(
                $message,
                $exception->getValidationErrors(),
                $errorType
            );
        }

        // Handle Laravel validation exceptions
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->handleValidationError(
                'Validation failed',
                $exception->errors()
            );
        }

        // Handle model not found exceptions
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $statusCode = 404;
            $errorType = 'not_found';
            $message = 'Booking not found';
        }

        // Handle database exceptions
        if ($exception instanceof \Illuminate\Database\QueryException) {
            $statusCode = 500;
            $errorType = 'database_error';
            $message = 'Database error occurred';
        }

        Log::error('Booking error occurred', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_type' => $errorType,
            'errors' => [$message]
        ], $statusCode);
    }

    /**
     * Handle success responses
     */
    protected function handleSuccess(
        $data = null,
        string $message = 'Operation completed successfully',
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get localized error message
     */
    protected function getLocalizedMessage(string $key, array $params = []): string
    {
        $locale = app()->getLocale();
        
        // Try to get localized message
        $message = __("booking.errors.{$key}", $params);
        
        // If translation not found, return the key
        if ($message === "booking.errors.{$key}") {
            return ucfirst(str_replace('_', ' ', $key));
        }
        
        return $message;
    }
}