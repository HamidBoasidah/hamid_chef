# Design Document

## Overview

هذا التصميم يوفر بنية معمارية نظيفة ومتسقة لمعالجة الأخطاء والاستجابات في Moon Chef API. يعتمد التصميم على مبدأ DRY (Don't Repeat Yourself) من خلال استخدام Traits قابلة لإعادة الاستخدام، واستثناءات مخصصة واضحة، ومعالج أخطاء مركزي.

الهدف الرئيسي هو توحيد طريقة التعامل مع الأخطاء والاستجابات عبر جميع Controllers، مما يجعل الكود أكثر قابلية للصيانة والتوسع.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        API Request                          │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                    Middleware Layer                         │
│              (Authentication, Validation)                   │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Uses Traits:                                        │   │
│  │  - ExceptionHandler (error throwing)                 │   │
│  │  - SuccessResponse (response formatting)             │   │
│  │  - CanFilter (filtering & search)                    │   │
│  │  - CanLoadRelationships (eager loading)              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────┬───────────────────────────────────────┘
                      │
        ┌─────────────┴─────────────┐
        │                           │
        ▼                           ▼
┌───────────────┐          ┌────────────────┐
│   Success     │          │   Exception    │
│   Response    │          │    Thrown      │
└───────┬───────┘          └────────┬───────┘
        │                           │
        │                           ▼
        │                  ┌────────────────────┐
        │                  │  Custom Exception  │
        │                  │  (NotFoundException,│
        │                  │  BusinessLogic,    │
        │                  │  ResourceInUse)    │
        │                  └────────┬───────────┘
        │                           │
        │                           ▼
        │                  ┌────────────────────┐
        │                  │  Exception Handler │
        │                  │  (Global Handler)  │
        │                  └────────┬───────────┘
        │                           │
        └───────────┬───────────────┘
                    │
                    ▼
        ┌───────────────────────┐
        │   JSON API Response   │
        │  {success, message,   │
        │   data, status_code}  │
        └───────────────────────┘
```

### Layer Responsibilities

1. **Controller Layer**: يستقبل الطلبات، يستخدم Traits للمعالجة، يرمي الاستثناءات أو يرجع الاستجابات
2. **Traits Layer**: توفر دوال مساعدة قابلة لإعادة الاستخدام
3. **Exception Layer**: استثناءات مخصصة مع رسائل واضحة وأكواد خطأ محددة
4. **Handler Layer**: يلتقط جميع الاستثناءات ويحولها لاستجابات JSON موحدة

## Components and Interfaces

### 1. Custom Exceptions

#### Base Exception: ApplicationException

```php
class ApplicationException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    
    public function __construct(string $message, int $statusCode, string $errorCode)
    public function render(Request $request): JsonResponse
    public function getStatusCode(): int
    public function getErrorCode(): string
}
```

#### Specific Exceptions

```php
// 404 - Resource Not Found
class NotFoundException extends ApplicationException
{
    public function __construct(string $message = 'المورد المطلوب غير موجود')
}

// 400 - Business Logic Error
class BusinessLogicException extends ApplicationException
{
    public function __construct(string $message = 'خطأ في منطق الأعمال')
}

// 401 - Unauthorized
class UnauthorizedException extends ApplicationException
{
    public function __construct(string $message = 'غير مصرح لك بالوصول لهذا المورد')
}

// 403 - Forbidden
class ForbiddenException extends ApplicationException
{
    public function __construct(string $message = 'ممنوع الوصول لهذا المورد')
}

// 409 - Resource In Use (Foreign Key Constraint)
class ResourceInUseException extends ApplicationException
{
    public function __construct(string $message = 'المورد مستخدم ولا يمكن حذفه')
}

// 422 - Validation Error
class ValidationException extends BaseValidationException
{
    public function render(Request $request): JsonResponse
}
```

### 2. ExceptionHandler Trait

```php
trait ExceptionHandler
{
    // Throw specific exceptions
    protected function throwNotFoundException(string $message): void
    protected function throwBusinessLogicException(string $message): void
    protected function throwUnauthorizedException(string $message): void
    protected function throwForbiddenException(string $message): void
    protected function throwResourceInUseException(string $message): void
    
    // Validation helpers
    protected function findOrFail(?Model $model, string $message): Model
    protected function validateBusinessLogic(bool $condition, string $message): void
    
    // Database exception handling
    protected function handleDatabaseException(
        QueryException $e, 
        Model $model, 
        array $relationshipChecks = []
    ): void
}
```

**handleDatabaseException Method Details:**

```php
protected function handleDatabaseException(
    QueryException $e, 
    Model $model, 
    array $relationshipChecks = []
): void {
    // Log the error with full context
    Log::error('Database exception during operation', [
        'model' => get_class($model),
        'model_id' => $model->id ?? null,
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage(),
        'sql' => $e->getSql(),
        'bindings' => $e->getBindings(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Check for foreign key constraint violation
    if ($e->getCode() == 23000) {
        // Check if it's a duplicate entry error
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            throw new BusinessLogicException('البيانات المراد إدخالها موجودة بالفعل');
        }
        
        // Otherwise it's a foreign key constraint
        $message = $this->buildRelationshipErrorMessage($model, $relationshipChecks);
        throw new ResourceInUseException($message);
    }
    
    // Re-throw for other database errors
    throw $e;
}

protected function buildRelationshipErrorMessage(
    Model $model, 
    array $relationshipChecks
): string {
    $modelName = class_basename($model);
    $arabicModelName = $this->getArabicModelName($modelName);
    
    foreach ($relationshipChecks as $relationship => $arabicName) {
        if ($model->$relationship()->exists()) {
            return "لا يمكن حذف {$arabicModelName} مرتبط بـ {$arabicName}";
        }
    }
    
    return "لا يمكن حذف {$arabicModelName} لوجود مراجع مرتبطة به";
}
```

### 3. SuccessResponse Trait

```php
trait SuccessResponse
{
    // Base success response
    protected function successResponse(
        $data = null, 
        string $message = 'تمت العملية بنجاح', 
        int $statusCode = 200
    ): JsonResponse
    
    // Specific success responses
    protected function createdResponse($data, string $message): JsonResponse  // 201
    protected function updatedResponse($data, string $message): JsonResponse  // 200
    protected function deletedResponse(string $message): JsonResponse         // 200
    protected function activatedResponse($data, string $message): JsonResponse
    protected function deactivatedResponse($data, string $message): JsonResponse
    
    // Collection responses
    protected function collectionResponse($collection, string $message): JsonResponse
    protected function resourceResponse($resource, string $message): JsonResponse
}
```

**collectionResponse Method Details:**

```php
protected function collectionResponse($collection, string $message): JsonResponse
{
    // Check if paginated
    if ($collection instanceof Paginator || $collection instanceof LengthAwarePaginator) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status_code' => 200,
            'data' => $collection->items(),
            'pagination' => [
                'current_page' => $collection->currentPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
                'last_page' => $collection->lastPage(),
            ]
        ], 200);
    }
    
    // Non-paginated collection
    return $this->successResponse($collection, $message, 200);
}
```

### 4. CanFilter Trait

```php
trait CanFilter
{
    // Main filter method
    protected function applyFilters(
        Builder $query, 
        Request $request, 
        array $searchableFields = [], 
        array $foreignKeyFilters = []
    ): Builder
    
    // Specific filter methods
    protected function applyTextSearch(Builder $query, Request $request, array $fields): Builder
    protected function applyForeignKeyFilters(Builder $query, Request $request, array $filters): Builder
    protected function applyDateFilter(Builder $query, Request $request, string $dateField = 'date'): Builder
    protected function applyRoleBasedFilter(Builder $query, Request $request): Builder
    
    // Configuration methods (to be overridden in controllers)
    protected function getSearchableFields(): array
    protected function getForeignKeyFilters(): array
}
```

### 5. Global Exception Handler

```php
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }
        return parent::render($request, $e);
    }
    
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Handle custom exceptions
        if ($e instanceof ApplicationException || $e instanceof ApplicationException) {
            return $e->render($request);
        }
        
        // Handle Laravel exceptions
        if ($e instanceof ValidationException) { /* ... */ }
        if ($e instanceof AuthenticationException) { /* ... */ }
        if ($e instanceof ModelNotFoundException) { /* ... */ }
        if ($e instanceof QueryException) { /* ... */ }
        if ($e instanceof NotFoundHttpException) { /* ... */ }
        if ($e instanceof MethodNotAllowedHttpException) { /* ... */ }
        
        // Handle general exceptions
        return response()->json([
            'success' => false,
            'message' => config('app.debug') ? $e->getMessage() : 'حدث خطأ غير متوقع',
            'error_code' => 'INTERNAL_SERVER_ERROR',
            'status_code' => 500,
            'timestamp' => now()->toISOString(),
        ], 500);
    }
}
```

## Data Models

### API Response Structure

#### Success Response

```json
{
  "success": true,
  "message": "تم جلب البيانات بنجاح",
  "status_code": 200,
  "data": {
    "id": 1,
    "name": "Example"
  }
}
```

#### Success Response with Pagination

```json
{
  "success": true,
  "message": "تم جلب قائمة العناوين بنجاح",
  "status_code": 200,
  "data": [
    {"id": 1, "street": "شارع الملك"},
    {"id": 2, "street": "شارع الأمير"}
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3
  }
}
```

#### Error Response

```json
{
  "success": false,
  "message": "المورد المطلوب غير موجود",
  "error_code": "NOT_FOUND",
  "status_code": 404,
  "timestamp": "2025-12-06T10:30:00.000000Z"
}
```

#### Validation Error Response

```json
{
  "success": false,
  "message": "بيانات غير صحيحة",
  "error_code": "VALIDATION_ERROR",
  "status_code": 422,
  "errors": {
    "street": ["حقل الشارع مطلوب"],
    "governorate_id": ["حقل المحافظة مطلوب"]
  },
  "timestamp": "2025-12-06T10:30:00.000000Z"
}
```

### Exception Error Codes

| Error Code | Status Code | Description |
|------------|-------------|-------------|
| NOT_FOUND | 404 | المورد غير موجود |
| BUSINESS_LOGIC_ERROR | 400 | خطأ في منطق الأعمال |
| UNAUTHORIZED | 401 | غير مصرح بالدخول |
| FORBIDDEN | 403 | ممنوع الوصول |
| RESOURCE_IN_USE | 409 | المورد مستخدم ولا يمكن حذفه |
| VALIDATION_ERROR | 422 | خطأ في التحقق من البيانات |
| DUPLICATE_ENTRY | 422 | البيانات موجودة مسبقاً |
| DATABASE_ERROR | 500 | خطأ في قاعدة البيانات |
| INTERNAL_SERVER_ERROR | 500 | خطأ داخلي في الخادم |



## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Exception Response Structure Consistency
*For any* exception thrown in a controller, the JSON response should always contain the fields: success (false), message, error_code, status_code, and timestamp
**Validates: Requirements 1.1**

### Property 2: Custom Exception Message Preservation
*For any* custom exception created with an Arabic message, rendering that exception should return a response containing the exact same message
**Validates: Requirements 2.6**

### Property 3: Custom Exception Structure Consistency
*For any* custom exception (NotFoundException, BusinessLogicException, etc.), the rendered response should have the same JSON structure with success, message, error_code, status_code, and timestamp fields
**Validates: Requirements 2.7**

### Property 4: NotFoundException Status Code
*For any* NotFoundException thrown with any message, the response status code should always be 404
**Validates: Requirements 1.3, 2.1**

### Property 5: BusinessLogicException Status Code
*For any* BusinessLogicException thrown with any message, the response status code should always be 400
**Validates: Requirements 1.4, 2.2**

### Property 6: ValidationException Status Code and Structure
*For any* validation error, the response should have status code 422, error_code 'VALIDATION_ERROR', and an errors object containing field-specific error messages
**Validates: Requirements 1.2**

### Property 7: findOrFail Throws on Null
*For any* null model passed to findOrFail, the method should throw NotFoundException
**Validates: Requirements 3.6**

### Property 8: validateBusinessLogic Throws on False
*For any* false condition passed to validateBusinessLogic with a message, the method should throw BusinessLogicException with that message
**Validates: Requirements 3.7**

### Property 9: createdResponse Status Code
*For any* data and message passed to createdResponse, the response status code should always be 201
**Validates: Requirements 4.2**

### Property 10: updatedResponse Status Code
*For any* data and message passed to updatedResponse, the response status code should always be 200
**Validates: Requirements 4.3**

### Property 11: deletedResponse Status Code
*For any* message passed to deletedResponse, the response status code should always be 200
**Validates: Requirements 4.4**

### Property 12: Paginated Collection Metadata
*For any* paginated collection passed to collectionResponse, the response should include pagination metadata with current_page, per_page, total, and last_page fields
**Validates: Requirements 4.6**

### Property 13: Foreign Key Constraint Detection
*For any* QueryException with error code 23000 (excluding duplicate entry), handleDatabaseException should throw ResourceInUseException
**Validates: Requirements 5.1**

### Property 14: Duplicate Entry Detection
*For any* QueryException with error code 23000 containing 'Duplicate entry' in the message, the system should return status code 422 with error_code 'DUPLICATE_ENTRY'
**Validates: Requirements 5.2**

### Property 15: Database Exception Logging
*For any* database exception handled, the log should contain model class, model ID, error code, error message, SQL query, bindings, and trace
**Validates: Requirements 5.4**

### Property 16: Text Search Across Fields
*For any* search term and set of searchable fields, applyTextSearch should add WHERE clauses that check all specified fields using LIKE operator
**Validates: Requirements 7.2**

### Property 17: Boolean String Conversion
*For any* string boolean value ('true', 'false', '0', '1') in foreign key filters, the system should convert it to an actual boolean or integer value
**Validates: Requirements 7.5**

### Property 18: Date Range Filtering
*For any* from_date and to_date parameters, applyDateFilter should filter records where the date field is between the specified range (inclusive)
**Validates: Requirements 7.4**

### Property 19: Handler JSON Response for API Requests
*For any* exception occurring in a request that expects JSON, the Handler should return a JSON response (not HTML)
**Validates: Requirements 8.1**

### Property 20: Handler ValidationException Handling
*For any* ValidationException caught by the Handler, the response should have status code 422 and include the validation errors array
**Validates: Requirements 8.2**

### Property 21: Handler AuthenticationException Handling
*For any* AuthenticationException caught by the Handler, the response should have status code 401 and error_code 'UNAUTHENTICATED'
**Validates: Requirements 8.3**

### Property 22: Handler ModelNotFoundException Handling
*For any* ModelNotFoundException caught by the Handler, the response should have status code 404 and error_code 'MODEL_NOT_FOUND'
**Validates: Requirements 8.4**

### Property 23: Backward Compatibility
*For any* existing API endpoint, after applying the new exception handling and response patterns, the response structure for successful operations should remain the same
**Validates: Requirements 10.5**

## Error Handling

### Exception Hierarchy

```
Exception (PHP Base)
    │
    ├── ApplicationException (Base Custom Exception)
    │   ├── NotFoundException (404)
    │   ├── BusinessLogicException (400)
    │   ├── UnauthorizedException (401)
    │   ├── ForbiddenException (403)
    │   └── ResourceInUseException (409)
    │
    └── Illuminate\Validation\ValidationException
        └── App\Exceptions\ValidationException (422)
```

### Error Handling Flow

1. **Controller Level**: 
   - Use trait methods to throw specific exceptions
   - Validate business logic before operations
   - Check relationships before deletion
   - Wrap database operations in try-catch

2. **Exception Level**:
   - Custom exceptions render themselves with consistent structure
   - Include Arabic error messages
   - Provide specific error codes

3. **Handler Level**:
   - Catch all unhandled exceptions
   - Transform Laravel exceptions to consistent format
   - Log errors appropriately
   - Return JSON for API requests

### Logging Strategy

```php
// Database errors - Full context
Log::error('Database exception', [
    'model' => get_class($model),
    'model_id' => $model->id,
    'error_code' => $e->getCode(),
    'sql' => $e->getSql(),
    'bindings' => $e->getBindings(),
    'trace' => $e->getTraceAsString()
]);

// Deletion attempts - Resource context
Log::warning('Deletion prevented', [
    'model' => get_class($model),
    'model_id' => $model->id,
    'reason' => 'Has related records',
    'relationships' => $relationshipNames
]);

// Business logic violations - Context
Log::info('Business logic validation failed', [
    'condition' => $conditionDescription,
    'message' => $errorMessage,
    'context' => $additionalContext
]);
```

## Testing Strategy

### Unit Testing

Unit tests will verify specific behaviors of individual components:

1. **Exception Tests**:
   - Test each custom exception returns correct status code
   - Test exception messages are preserved
   - Test exception rendering produces correct JSON structure

2. **Trait Method Tests**:
   - Test throwNotFoundException with various messages
   - Test findOrFail with null and valid models
   - Test validateBusinessLogic with true/false conditions
   - Test response methods return correct status codes
   - Test collectionResponse handles paginated/non-paginated data

3. **Handler Tests**:
   - Test Handler catches and transforms each exception type
   - Test JSON responses for API requests
   - Test HTML responses for web requests

4. **Filter Tests**:
   - Test text search with multiple fields
   - Test foreign key filtering
   - Test date range filtering
   - Test boolean string conversion

### Property-Based Testing

Property-based tests will verify universal properties across many random inputs using a PHP property testing library (e.g., Eris or PHPUnit with data providers):

1. **Exception Response Structure** (Property 1):
   - Generate random exceptions
   - Verify all responses have required fields

2. **Status Code Consistency** (Properties 4, 5, 9, 10, 11):
   - Generate random data for each response type
   - Verify status codes are always correct

3. **Message Preservation** (Property 2):
   - Generate random Arabic messages
   - Verify messages are preserved in responses

4. **Pagination Metadata** (Property 12):
   - Generate random paginated collections
   - Verify metadata fields are always present

5. **Boolean Conversion** (Property 17):
   - Generate random boolean string representations
   - Verify conversion to actual booleans

6. **Backward Compatibility** (Property 23):
   - Generate random valid requests
   - Compare old and new response structures

### Integration Testing

Integration tests will verify the complete flow from controller to response:

1. **CRUD Operations**:
   - Test index with various filters
   - Test store with valid/invalid data
   - Test show with existing/non-existing resources
   - Test update with valid/invalid data
   - Test destroy with/without relationships

2. **Error Scenarios**:
   - Test deletion with foreign key constraints
   - Test duplicate entry errors
   - Test validation errors
   - Test authentication/authorization errors

3. **Response Format**:
   - Verify all successful operations return consistent structure
   - Verify all error operations return consistent structure
   - Verify pagination works correctly

### Test Configuration

- Property-based tests should run minimum 100 iterations
- Each property test must reference the design document property number
- Tag format: `**Feature: clean-api-architecture, Property {number}: {property_text}**`
- Use factories for generating test data
- Use in-memory SQLite for database tests

## Implementation Notes

### Controller Pattern

All controllers should follow this pattern:

```php
class AddressController extends Controller
{
    use ExceptionHandler, SuccessResponse, CanFilter, CanLoadRelationships;
    
    private array $relations = ['governorate', 'district', 'area', 'user'];
    
    public function index(Request $request)
    {
        $query = $this->loadRelationships(Address::query());
        $query = $this->applyFilters($query, $request, 
            $this->getSearchableFields(), 
            $this->getForeignKeyFilters()
        );
        
        $paginator = $query->latest()->paginate(10);
        $paginator->getCollection()->transform(fn($item) => new AddressResource($item));
        
        return $this->collectionResponse($paginator, 'تم جلب قائمة العناوين بنجاح');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
            // ... other rules
        ]);
        
        $address = Address::create($validated);
        
        return $this->createdResponse(
            new AddressResource($this->loadRelationships($address)),
            'تم إضافة العنوان بنجاح'
        );
    }
    
    public function show(Address $address)
    {
        $this->findOrFail($address, 'العنوان المطلوب غير موجود');
        
        return $this->resourceResponse(
            new AddressResource($this->loadRelationships($address)),
            'تم جلب بيانات العنوان بنجاح'
        );
    }
    
    public function update(Request $request, Address $address)
    {
        $this->findOrFail($address, 'العنوان المطلوب غير موجود');
        
        $validated = $request->validate([
            'street' => 'sometimes|string|max:255',
            // ... other rules
        ]);
        
        $address->update($validated);
        
        return $this->updatedResponse(
            new AddressResource($this->loadRelationships($address)),
            'تم تحديث بيانات العنوان بنجاح'
        );
    }
    
    public function destroy(Address $address)
    {
        $this->findOrFail($address, 'العنوان المطلوب غير موجود');
        
        try {
            // Check relationships
            if ($address->bookings()->exists()) {
                $this->throwResourceInUseException('لا يمكن حذف عنوان مرتبط بحجوزات');
            }
            
            $address->delete();
            
            return $this->deletedResponse('تم حذف العنوان بنجاح');
            
        } catch (QueryException $e) {
            $this->handleDatabaseException($e, $address, [
                'bookings' => 'حجوزات'
            ]);
        }
    }
    
    protected function getSearchableFields(): array
    {
        return ['street', 'building_number', 'floor_number'];
    }
    
    protected function getForeignKeyFilters(): array
    {
        return [
            'governorate_id' => 'governorate_id',
            'district_id' => 'district_id',
            'area_id' => 'area_id',
            'user_id' => 'user_id',
        ];
    }
}
```

### Migration Path for Existing Controllers

1. Add traits to controller class
2. Replace manual response building with trait methods
3. Replace manual exception throwing with trait methods
4. Add relationship checking before deletion
5. Add database exception handling with logging
6. Test thoroughly to ensure backward compatibility

### Performance Considerations

1. Use `exists()` instead of `count()` for relationship checks
2. Use eager loading to avoid N+1 queries
3. Index foreign key columns in database
4. Cache frequently accessed data
5. Use pagination for large collections

### Security Considerations

1. Never expose sensitive data in error messages
2. Log full error details but return generic messages to clients
3. Validate all input data before processing
4. Use Laravel's built-in CSRF protection
5. Implement rate limiting on API endpoints
6. Use proper authentication and authorization checks
