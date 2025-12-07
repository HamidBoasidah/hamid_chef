# Requirements Document

## Introduction

هذا المستند يحدد متطلبات تحسين نظام معالجة الأخطاء والاستجابات في مشروع Moon Chef API. الهدف هو توحيد طريقة التعامل مع الأخطاء والاستجابات عبر جميع Controllers في المشروع، مع التركيز على معالجة أخطاء قاعدة البيانات، التحقق من صحة البيانات، والاستجابات الموحدة.

## Glossary

- **Exception Handler**: نظام معالجة الأخطاء المركزي الذي يلتقط ويعالج جميع الأخطاء في التطبيق
- **Custom Exception**: استثناء مخصص يرث من الاستثناءات الأساسية ويوفر رسائل خطأ محددة
- **Trait**: وحدة قابلة لإعادة الاستخدام تحتوي على دوال مساعدة يمكن استخدامها في Controllers متعددة
- **API Response**: استجابة JSON موحدة تحتوي على حالة النجاح، الرسالة، البيانات، ورمز الحالة
- **Business Logic Validation**: التحقق من قواعد العمل قبل تنفيذ العمليات (مثل منع حذف سجل مرتبط بسجلات أخرى)
- **Foreign Key Constraint**: قيد في قاعدة البيانات يمنع حذف أو تعديل سجل مرتبط بسجلات أخرى
- **Query Exception**: خطأ يحدث عند تنفيذ استعلام قاعدة البيانات
- **Resource**: مورد API يمثل كيان في النظام (مثل User، Chef، Booking)

## Requirements

### Requirement 1

**User Story:** كمطور API، أريد نظام معالجة أخطاء موحد، حتى أتمكن من إرجاع استجابات خطأ متسقة ومفهومة للعملاء.

#### Acceptance Criteria

1. WHEN an exception occurs in any controller THEN the system SHALL return a JSON response with success flag, message, error_code, status_code, and timestamp
2. WHEN a validation error occurs THEN the system SHALL return status code 422 with error_code 'VALIDATION_ERROR' and detailed validation errors
3. WHEN a resource is not found THEN the system SHALL return status code 404 with error_code 'NOT_FOUND'
4. WHEN a business logic violation occurs THEN the system SHALL return status code 400 with error_code 'BUSINESS_LOGIC_ERROR'
5. WHEN a database constraint violation occurs THEN the system SHALL return status code 409 or 422 with appropriate error_code and Arabic message

### Requirement 2

**User Story:** كمطور API، أريد استثناءات مخصصة لسيناريوهات الأخطاء المختلفة، حتى أتمكن من رفع أخطاء واضحة ومحددة في الكود.

#### Acceptance Criteria

1. THE system SHALL provide NotFoundException for missing resources with status code 404
2. THE system SHALL provide BusinessLogicException for business rule violations with status code 400
3. THE system SHALL provide UnauthorizedException for authentication failures with status code 401
4. THE system SHALL provide ForbiddenException for authorization failures with status code 403
5. THE system SHALL provide ResourceInUseException for foreign key constraint violations with status code 409
6. WHEN creating custom exceptions THEN the system SHALL allow customizable error messages in Arabic
7. WHEN rendering custom exceptions THEN the system SHALL return consistent JSON structure with error_code and timestamp

### Requirement 3

**User Story:** كمطور API، أريد Trait لمعالجة الأخطاء، حتى أتمكن من استخدام دوال مساعدة موحدة في جميع Controllers.

#### Acceptance Criteria

1. THE ExceptionHandler trait SHALL provide throwNotFoundException method that accepts custom message
2. THE ExceptionHandler trait SHALL provide throwBusinessLogicException method that accepts custom message
3. THE ExceptionHandler trait SHALL provide throwUnauthorizedException method that accepts custom message
4. THE ExceptionHandler trait SHALL provide throwForbiddenException method that accepts custom message
5. THE ExceptionHandler trait SHALL provide throwResourceInUseException method that accepts custom message
6. THE ExceptionHandler trait SHALL provide findOrFail method that validates model existence
7. THE ExceptionHandler trait SHALL provide validateBusinessLogic method that throws exception when condition is false
8. THE ExceptionHandler trait SHALL provide handleDatabaseException method that catches and transforms QueryException to appropriate custom exceptions

### Requirement 4

**User Story:** كمطور API، أريد Trait للاستجابات الناجحة، حتى أتمكن من إرجاع استجابات JSON موحدة لجميع العمليات الناجحة.

#### Acceptance Criteria

1. THE SuccessResponse trait SHALL provide successResponse method with data, message, and status_code
2. THE SuccessResponse trait SHALL provide createdResponse method with status code 201
3. THE SuccessResponse trait SHALL provide updatedResponse method with status code 200
4. THE SuccessResponse trait SHALL provide deletedResponse method with status code 200
5. THE SuccessResponse trait SHALL provide collectionResponse method that handles paginated and non-paginated collections
6. WHEN returning paginated collection THEN the system SHALL include pagination metadata (current_page, per_page, total, last_page)
7. THE SuccessResponse trait SHALL provide activatedResponse and deactivatedResponse methods for status toggle operations

### Requirement 5

**User Story:** كمطور API، أريد معالجة أخطاء قاعدة البيانات بشكل ذكي، حتى أتمكن من إرجاع رسائل خطأ واضحة بالعربية للمستخدمين.

#### Acceptance Criteria

1. WHEN a foreign key constraint violation occurs (error code 23000) THEN the system SHALL return ResourceInUseException with Arabic message
2. WHEN a duplicate entry error occurs THEN the system SHALL return status code 422 with error_code 'DUPLICATE_ENTRY' and Arabic message
3. WHEN a database connection error occurs THEN the system SHALL return status code 500 with error_code 'DATABASE_ERROR'
4. THE system SHALL log all database exceptions with full context (SQL query, bindings, error code, trace)
5. THE system SHALL detect specific constraint names and return customized error messages

### Requirement 6

**User Story:** كمطور API، أريد معالجة عمليات الحذف بشكل آمن، حتى أتمكن من منع حذف السجلات المرتبطة بسجلات أخرى.

#### Acceptance Criteria

1. WHEN deleting a resource THEN the system SHALL check all related resources before deletion
2. WHEN a resource has related records THEN the system SHALL throw ResourceInUseException with specific Arabic message
3. WHEN checking relationships THEN the system SHALL use exists() method for performance
4. WHEN a database constraint prevents deletion THEN the system SHALL catch QueryException and throw ResourceInUseException
5. THE system SHALL log all deletion attempts with resource ID and error details

### Requirement 7

**User Story:** كمطور API، أريد Trait للفلترة والبحث، حتى أتمكن من تطبيق فلاتر موحدة على جميع الموارد.

#### Acceptance Criteria

1. THE CanFilter trait SHALL provide applyFilters method that accepts query, request, searchable fields, and foreign key filters
2. WHEN applying text search THEN the system SHALL search across all specified searchable fields using LIKE operator
3. WHEN applying foreign key filters THEN the system SHALL filter by exact match on specified fields
4. WHEN applying date range filter THEN the system SHALL filter by from_date and to_date parameters
5. THE CanFilter trait SHALL convert string boolean values ('true', 'false', '0', '1') to actual boolean values
6. THE CanFilter trait SHALL provide applyRoleBasedFilter method for role-specific filtering

### Requirement 8

**User Story:** كمطور API، أريد معالجة مركزية للأخطاء في Handler، حتى يتم التقاط جميع الأخطاء غير المعالجة وإرجاع استجابات موحدة.

#### Acceptance Criteria

1. WHEN an API request expects JSON THEN the Handler SHALL return JSON error responses
2. WHEN a ValidationException occurs THEN the Handler SHALL return 422 with validation errors array
3. WHEN an AuthenticationException occurs THEN the Handler SHALL return 401 with Arabic message
4. WHEN a ModelNotFoundException occurs THEN the Handler SHALL return 404 with Arabic message
5. WHEN a NotFoundHttpException occurs THEN the Handler SHALL return 404 with 'ROUTE_NOT_FOUND' error code
6. WHEN a MethodNotAllowedHttpException occurs THEN the Handler SHALL return 405 with Arabic message
7. WHEN an unhandled exception occurs THEN the Handler SHALL return 500 with generic message (or detailed message in debug mode)

### Requirement 9

**User Story:** كمطور API، أريد استخدام نمط موحد في جميع Controllers، حتى يكون الكود متسقاً وسهل الصيانة.

#### Acceptance Criteria

1. THE controller SHALL use ExceptionHandler, SuccessResponse, and CanFilter traits
2. WHEN implementing index method THEN the controller SHALL apply filters and return collectionResponse
3. WHEN implementing store method THEN the controller SHALL validate data and return createdResponse
4. WHEN implementing show method THEN the controller SHALL use findOrFail and return resourceResponse
5. WHEN implementing update method THEN the controller SHALL use findOrFail, validate data, and return updatedResponse
6. WHEN implementing destroy method THEN the controller SHALL check relationships, handle database exceptions, and return deletedResponse
7. THE controller SHALL define getSearchableFields and getForeignKeyFilters methods for filtering

### Requirement 10

**User Story:** كمطور API، أريد تحسين معالجة الأخطاء في Controllers الموجودة، حتى تتبع النمط الموحد الجديد.

#### Acceptance Criteria

1. WHEN updating existing controllers THEN the system SHALL replace manual exception handling with trait methods
2. WHEN updating existing controllers THEN the system SHALL replace manual response building with trait methods
3. WHEN updating existing controllers THEN the system SHALL add proper relationship checking before deletion
4. WHEN updating existing controllers THEN the system SHALL add database exception handling with logging
5. THE updated controllers SHALL maintain backward compatibility with existing API responses
