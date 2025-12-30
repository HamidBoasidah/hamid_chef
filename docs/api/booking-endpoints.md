# Booking API Documentation

## Overview

The Booking API provides endpoints for managing chef bookings with advanced conflict prevention and time gap enforcement. All endpoints require authentication via Sanctum tokens.

## Base URL

```
https://your-domain.com/api
```

## Authentication

All endpoints require a valid Sanctum token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Rate Limiting

Different endpoints have different rate limits:

- **General API**: 100 requests per minute
- **Booking Creation**: 10 requests per minute
- **Availability Check**: 60 requests per minute

Rate limit headers are included in responses:
- `X-RateLimit-Limit`: Maximum requests allowed
- `X-RateLimit-Remaining`: Remaining requests
- `X-RateLimit-Reset`: Seconds until reset

## Endpoints

### 1. List Bookings

**GET** `/api/bookings`

Retrieve a paginated list of bookings for the authenticated user.

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Number of items per page (default: 15) |
| `page` | integer | Page number (default: 1) |

#### Response

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "customer_id": 1,
        "chef_id": 2,
        "chef_service_id": 3,
        "date": "2025-12-25",
        "start_time": "14:00",
        "hours_count": 3,
        "total_amount": "450.00",
        "booking_status": "pending",
        "payment_status": "pending",
        "created_at": "2025-12-20T10:30:00Z",
        "customer": {
          "id": 1,
          "first_name": "Ahmed",
          "last_name": "Ali"
        },
        "chef": {
          "id": 2,
          "name": "Chef Mohammed"
        }
      }
    ],
    "per_page": 15,
    "total": 1
  },
  "message": "Bookings retrieved successfully"
}
```

### 2. Create Booking

**POST** `/api/bookings`

Create a new booking with conflict prevention and time gap validation.

#### Request Body

```json
{
  "chef_id": 2,
  "chef_service_id": 3,
  "address_id": 1,
  "date": "2025-12-25",
  "start_time": "14:00",
  "hours_count": 3,
  "number_of_guests": 4,
  "service_type": "hourly",
  "unit_price": "150.00",
  "extra_guests_count": 1,
  "extra_guests_amount": "50.00",
  "total_amount": "500.00",
  "notes": "Special dietary requirements"
}
```

#### Validation Rules

| Field | Type | Rules |
|-------|------|-------|
| `chef_id` | integer | required, exists:chefs,id |
| `chef_service_id` | integer | required, exists:chef_services,id |
| `address_id` | integer | nullable, exists:addresses,id |
| `date` | date | required, after_or_equal:today |
| `start_time` | time | required, format:H:i |
| `hours_count` | integer | required, min:1, max:12 |
| `number_of_guests` | integer | required, min:1, max:50 |
| `service_type` | string | required, in:hourly,package |
| `unit_price` | decimal | required, min:0 |
| `total_amount` | decimal | required, min:0 |

#### Success Response (201)

```json
{
  "success": true,
  "data": {
    "id": 1,
    "customer_id": 1,
    "chef_id": 2,
    "chef_service_id": 3,
    "date": "2025-12-25",
    "start_time": "14:00",
    "hours_count": 3,
    "total_amount": "500.00",
    "booking_status": "pending",
    "created_at": "2025-12-20T10:30:00Z"
  },
  "message": "Booking created successfully"
}
```

#### Conflict Error Response (409)

```json
{
  "success": false,
  "message": "Booking conflicts detected",
  "error_type": "booking_conflict",
  "errors": ["Booking conflicts with existing reservations"],
  "conflicting_bookings": [
    {
      "id": 5,
      "date": "2025-12-25",
      "start_time": "13:00",
      "end_time": "16:00",
      "hours_count": 3
    }
  ]
}
```

#### Time Gap Error Response (409)

```json
{
  "success": false,
  "message": "Time gap requirements not met",
  "error_type": "time_gap_violation",
  "errors": ["Minimum 2-hour gap required between bookings"],
  "conflicting_bookings": [
    {
      "id": 5,
      "date": "2025-12-25",
      "start_time": "12:00",
      "end_time": "13:00",
      "gap_hours": 1,
      "required_gap": 2,
      "violation_type": "insufficient_gap_before"
    }
  ]
}
```

### 3. Get Booking Details

**GET** `/api/bookings/{id}`

Retrieve detailed information about a specific booking.

#### Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "customer_id": 1,
    "chef_id": 2,
    "chef_service_id": 3,
    "address_id": 1,
    "date": "2025-12-25",
    "start_time": "14:00",
    "hours_count": 3,
    "number_of_guests": 4,
    "service_type": "hourly",
    "unit_price": "150.00",
    "extra_guests_count": 1,
    "extra_guests_amount": "50.00",
    "total_amount": "500.00",
    "commission_amount": "50.00",
    "booking_status": "pending",
    "payment_status": "pending",
    "notes": "Special dietary requirements",
    "created_at": "2025-12-20T10:30:00Z",
    "customer": {
      "id": 1,
      "first_name": "Ahmed",
      "last_name": "Ali",
      "email": "ahmed@example.com"
    },
    "chef": {
      "id": 2,
      "name": "Chef Mohammed"
    },
    "service": {
      "id": 3,
      "name": "Traditional Arabic Cuisine",
      "service_type": "hourly"
    },
    "address": {
      "id": 1,
      "address": "123 Main Street, Riyadh"
    }
  },
  "message": "Booking retrieved successfully"
}
```

### 4. Update Booking

**PUT** `/api/bookings/{id}`

Update an existing booking with conflict validation.

#### Request Body

Same as create booking, but all fields are optional.

#### Response

Same as get booking details.

### 5. Cancel Booking (Customer)

**POST** `/api/bookings/{id}/cancel-by-customer`

Cancel a booking by the customer only (sets status to `cancelled_by_customer`).

Allowed only when current `booking_status` is `pending` or `accepted`.

#### Response

```json
{
  "success": true,
  "message": "تم إلغاء الحجز بنجاح"
}
```

#### Validation Error (422)

```json
{
  "success": false,
  "message": "لا يمكن للعميل إلغاء الحجز في حالته الحالية",
  "errors": {
    "booking_status": [
      "الحالة الحالية لا تسمح بالإلغاء",
      "current_status: completed"
    ]
  }
}
```

> Note: The legacy route `DELETE /api/bookings/{id}` remains for backward compatibility and will determine cancellation actor automatically.

### 6. Check Chef Availability
### 7. Chef Booking Status Management

These endpoints are under the chef namespace and require the `user_role:chef` middleware.

Base path: `/api/chef/bookings/{id}`

- **POST** `/accept` → sets `booking_status` to `accepted`
  - Note: Allowed only when current `booking_status` is `pending`. Returns 422 otherwise.
- **POST** `/reject` → sets `booking_status` to `rejected` and `is_active` to `false`

  - Note: Allowed only when current `booking_status` is `pending`. Returns 422 otherwise.
- **POST** `/cancel` → sets `booking_status` to `cancelled_by_chef` and `is_active` to `false`
- **POST** `/complete` → sets `booking_status` to `completed`

  - Note: Both `cancel` and `complete` are allowed only when current `booking_status` is `accepted`. Returns 422 otherwise.

#### Success Response (example)

```json
{
  "success": true,
  "data": {
    "id": 1,
    "booking_status": "accepted"
  },
  "message": "تم قبول الحجز بنجاح"
}
```

**GET** `/api/chefs/{chef_id}/availability`

Check if a chef is available for a specific time slot.

#### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `date` | date | Yes | Booking date (YYYY-MM-DD) |
| `start_time` | time | Yes | Start time (HH:MM) |
| `hours_count` | integer | Yes | Duration in hours |
| `exclude_booking_id` | integer | No | Exclude specific booking from check |

#### Response

```json
{
  "success": true,
  "data": {
    "available": true,
    "errors": [],
    "conflicting_bookings": []
  },
  "message": "Time slot is available"
}
```

#### Unavailable Response

```json
{
  "success": true,
  "data": {
    "available": false,
    "errors": [
      "Booking conflicts with existing reservations",
      "Minimum 2-hour gap required between bookings"
    ],
    "conflicting_bookings": [
      {
        "id": 5,
        "date": "2025-12-25",
        "start_time": "13:00",
        "end_time": "16:00",
        "hours_count": 3
      }
    ]
  },
  "message": "Time slot is not available"
}
```

### 8. Get Chef Bookings

**GET** `/api/chefs/{chef_id}/bookings`

Get all bookings for a specific chef on a given date.

#### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `date` | date | Yes | Date to retrieve bookings for |

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "date": "2025-12-25",
      "start_time": "14:00",
      "end_time": "17:00",
      "hours_count": 3,
      "booking_status": "accepted",
      "customer_name": "Ahmed Ali"
    }
  ],
  "message": "Chef bookings retrieved successfully"
}
```

### 9. Validate Booking

**POST** `/api/bookings/validate`

Validate booking data without creating the booking.

#### Request Body

Same as create booking.

#### Response

Same as check availability endpoint.

## Error Responses

### Validation Error (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "error_type": "validation_error",
  "errors": {
    "chef_id": ["The chef field is required."],
    "date": ["The date must be a date after or equal to today."]
  }
}
```

### Authentication Error (401)

```json
{
  "success": false,
  "message": "Unauthorized access",
  "error_type": "authentication_error",
  "errors": ["Unauthorized access"]
}
```

### Rate Limit Error (429)

```json
{
  "success": false,
  "message": "Too many requests. Please try again later.",
  "error_type": "rate_limit_exceeded",
  "retry_after": 60
}
```

### Server Error (500)

```json
{
  "success": false,
  "message": "An error occurred while processing the booking",
  "error_type": "system_error",
  "errors": ["An error occurred while processing the booking"]
}
```

## Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 409 | Conflict |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Server Error |

## Booking Statuses

| Status | Description |
|--------|-------------|
| `pending` | Waiting for chef acceptance |
| `accepted` | Confirmed by chef |
| `rejected` | Declined by chef |
| `cancelled_by_customer` | Cancelled by customer |
| `cancelled_by_chef` | Cancelled by chef |
| `completed` | Service completed |

## Payment Statuses

| Status | Description |
|--------|-------------|
| `pending` | Payment not processed |
| `paid` | Payment successful |
| `refunded` | Payment refunded |
| `failed` | Payment failed |

## Examples

### Creating a Simple Booking

```bash
curl -X POST https://your-domain.com/api/bookings \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "chef_id": 2,
    "chef_service_id": 3,
    "date": "2025-12-25",
    "start_time": "14:00",
    "hours_count": 3,
    "number_of_guests": 4,
    "service_type": "hourly",
    "unit_price": "150.00",
    "total_amount": "450.00"
  }'
```

### Checking Availability

```bash
curl -X GET "https://your-domain.com/api/chefs/2/availability?date=2025-12-25&start_time=14:00&hours_count=3" \
  -H "Authorization: Bearer your-token"
```

### Getting Chef's Daily Schedule

```bash
curl -X GET "https://your-domain.com/api/chefs/2/bookings?date=2025-12-25" \
  -H "Authorization: Bearer your-token"
```