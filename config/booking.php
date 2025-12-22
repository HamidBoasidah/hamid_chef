<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the booking system,
    | including conflict prevention, time constraints, and business rules.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Time Gap Requirements
    |--------------------------------------------------------------------------
    |
    | Minimum time gap required between consecutive bookings for the same chef.
    | This ensures chefs have adequate time for preparation and travel.
    |
    */

    'minimum_gap_hours' => env('BOOKING_MINIMUM_GAP_HOURS', 2),

    /*
    |--------------------------------------------------------------------------
    | Booking Duration Limits
    |--------------------------------------------------------------------------
    |
    | Maximum number of hours allowed for a single booking session.
    | This prevents excessively long bookings that could impact scheduling.
    |
    */

    'max_booking_hours' => env('BOOKING_MAX_HOURS', 12),

    /*
    |--------------------------------------------------------------------------
    | Minimum Booking Duration
    |--------------------------------------------------------------------------
    |
    | Minimum number of hours required for package-type bookings.
    | Hourly bookings can be as short as 1 hour.
    |
    */

    'min_package_hours' => env('BOOKING_MIN_PACKAGE_HOURS', 2),

    /*
    |--------------------------------------------------------------------------
    | Advance Booking Period
    |--------------------------------------------------------------------------
    |
    | Maximum number of days in advance that bookings can be made.
    | This prevents bookings too far in the future.
    |
    */

    'advance_booking_days' => env('BOOKING_ADVANCE_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Operating Hours
    |--------------------------------------------------------------------------
    |
    | Define the allowed booking hours. Bookings outside these hours
    | will be rejected during validation.
    |
    */

    'operating_hours' => [
        'start' => env('BOOKING_START_HOUR', 8),  // 8 AM
        'end' => env('BOOKING_END_HOUR', 22),     // 10 PM
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Lock Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for database locking to prevent race conditions.
    | Lock timeout is in seconds.
    |
    */

    'lock_timeout_seconds' => env('BOOKING_LOCK_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Guest Limits
    |--------------------------------------------------------------------------
    |
    | Maximum number of guests allowed per booking and extra guests.
    |
    */

    'max_guests' => env('BOOKING_MAX_GUESTS', 50),
    'max_extra_guests' => env('BOOKING_MAX_EXTRA_GUESTS', 20),

    /*
    |--------------------------------------------------------------------------
    | Commission Configuration
    |--------------------------------------------------------------------------
    |
    | Default commission rate applied to bookings.
    | This can be overridden per chef or service.
    |
    */

    'default_commission_rate' => env('BOOKING_DEFAULT_COMMISSION_RATE', 0.10), // 10%

    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for pricing calculations and limits.
    |
    */

    'pricing' => [
        'max_amount' => env('BOOKING_MAX_AMOUNT', 9999999.99),
        'currency' => env('BOOKING_CURRENCY', 'SAR'),
        'decimal_places' => env('BOOKING_DECIMAL_PLACES', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Custom validation rules and messages for booking operations.
    |
    */

    'validation' => [
        'require_address' => env('BOOKING_REQUIRE_ADDRESS', false),
        'require_notes_on_cancel' => env('BOOKING_REQUIRE_CANCEL_NOTES', true),
        'allow_same_day_booking' => env('BOOKING_ALLOW_SAME_DAY', true),
        'allow_past_date_admin' => env('BOOKING_ALLOW_PAST_DATE_ADMIN', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Transitions
    |--------------------------------------------------------------------------
    |
    | Define valid status transitions for booking workflow.
    |
    */

    'status_transitions' => [
        'pending' => ['accepted', 'rejected', 'cancelled_by_customer', 'cancelled_by_chef', 'cancelled_by_admin'],
        'accepted' => ['completed', 'cancelled_by_customer', 'cancelled_by_chef', 'cancelled_by_admin'],
        'rejected' => [], // Final state
        'cancelled_by_customer' => [], // Final state
        'cancelled_by_chef' => [], // Final state
        'cancelled_by_admin' => [], // Final state
        'completed' => [], // Final state
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for booking-related notifications.
    |
    */

    'notifications' => [
        'send_confirmation' => env('BOOKING_SEND_CONFIRMATION', true),
        'send_reminder' => env('BOOKING_SEND_REMINDER', true),
        'reminder_hours_before' => env('BOOKING_REMINDER_HOURS', 24),
        'send_chef_notification' => env('BOOKING_SEND_CHEF_NOTIFICATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching booking-related data.
    |
    */

    'cache' => [
        'availability_ttl' => env('BOOKING_CACHE_AVAILABILITY_TTL', 300), // 5 minutes
        'chef_bookings_ttl' => env('BOOKING_CACHE_CHEF_BOOKINGS_TTL', 600), // 10 minutes
        'enable_caching' => env('BOOKING_ENABLE_CACHING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for booking API endpoints.
    |
    */

    'rate_limiting' => [
        'booking_creation' => env('BOOKING_RATE_LIMIT_CREATE', '10,1'), // 10 requests per minute
        'availability_check' => env('BOOKING_RATE_LIMIT_AVAILABILITY', '60,1'), // 60 requests per minute
        'general_api' => env('BOOKING_RATE_LIMIT_GENERAL', '100,1'), // 100 requests per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for booking-related logging.
    |
    */

    'logging' => [
        'log_conflicts' => env('BOOKING_LOG_CONFLICTS', true),
        'log_race_conditions' => env('BOOKING_LOG_RACE_CONDITIONS', true),
        'log_status_changes' => env('BOOKING_LOG_STATUS_CHANGES', true),
        'log_pricing_calculations' => env('BOOKING_LOG_PRICING', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific booking features.
    |
    */

    'features' => [
        'conflict_prevention' => env('BOOKING_ENABLE_CONFLICT_PREVENTION', true),
        'time_gap_enforcement' => env('BOOKING_ENABLE_TIME_GAP', true),
        'race_condition_protection' => env('BOOKING_ENABLE_RACE_PROTECTION', true),
        'automatic_pricing' => env('BOOKING_ENABLE_AUTO_PRICING', true),
        'availability_suggestions' => env('BOOKING_ENABLE_SUGGESTIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for testing environments.
    |
    */

    'testing' => [
        'disable_time_validation' => env('BOOKING_TESTING_DISABLE_TIME_VALIDATION', false),
        'mock_external_services' => env('BOOKING_TESTING_MOCK_SERVICES', false),
        'fast_conflict_check' => env('BOOKING_TESTING_FAST_CONFLICT_CHECK', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for performance optimization.
    |
    */

    'performance' => [
        'batch_size' => env('BOOKING_BATCH_SIZE', 100),
        'query_timeout' => env('BOOKING_QUERY_TIMEOUT', 30),
        'enable_query_optimization' => env('BOOKING_ENABLE_QUERY_OPTIMIZATION', true),
        'use_read_replicas' => env('BOOKING_USE_READ_REPLICAS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for multi-language support.
    |
    */

    'localization' => [
        'default_locale' => env('BOOKING_DEFAULT_LOCALE', 'ar'),
        'supported_locales' => ['ar', 'en'],
        'fallback_locale' => env('BOOKING_FALLBACK_LOCALE', 'en'),
        'date_format' => [
            'ar' => 'Y/m/d',
            'en' => 'm/d/Y',
        ],
        'time_format' => [
            'ar' => 'H:i',
            'en' => 'h:i A',
        ],
    ],

];