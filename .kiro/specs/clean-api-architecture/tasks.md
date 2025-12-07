# Implementation Plan

- [x] 1. Enhance Custom Exceptions
  - Add ResourceInUseException class for foreign key constraint violations
  - Ensure all custom exceptions support Arabic messages
  - Verify all exceptions render with consistent JSON structure
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

- [ ]* 1.1 Write property test for exception response structure
  - **Property 1: Exception Response Structure Consistency**
  - **Validates: Requirements 1.1**

- [ ]* 1.2 Write property test for custom exception message preservation
  - **Property 2: Custom Exception Message Preservation**
  - **Validates: Requirements 2.6**

- [ ]* 1.3 Write property test for exception status codes
  - **Property 4: NotFoundException Status Code**
  - **Property 5: BusinessLogicException Status Code**
  - **Validates: Requirements 1.3, 1.4, 2.1, 2.2**

- [x] 2. Enhance ExceptionHandler Trait
  - Add throwResourceInUseException method
  - Add handleDatabaseException method for QueryException handling
  - Add buildRelationshipErrorMessage helper method
  - Enhance existing methods with better error messages
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 5.1, 5.2, 5.4, 5.5_

- [ ]* 2.1 Write property test for findOrFail behavior
  - **Property 7: findOrFail Throws on Null**
  - **Validates: Requirements 3.6**

- [ ]* 2.2 Write property test for validateBusinessLogic behavior
  - **Property 8: validateBusinessLogic Throws on False**
  - **Validates: Requirements 3.7**

- [ ]* 2.3 Write property test for database exception handling
  - **Property 13: Foreign Key Constraint Detection**
  - **Property 14: Duplicate Entry Detection**
  - **Validates: Requirements 5.1, 5.2**

- [x] 3. Enhance SuccessResponse Trait
  - Verify all response methods return correct status codes
  - Ensure collectionResponse handles both paginated and non-paginated collections correctly
  - Add proper pagination metadata structure
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [ ]* 3.1 Write property test for response status codes
  - **Property 9: createdResponse Status Code**
  - **Property 10: updatedResponse Status Code**
  - **Property 11: deletedResponse Status Code**
  - **Validates: Requirements 4.2, 4.3, 4.4**

- [ ]* 3.2 Write property test for pagination metadata
  - **Property 12: Paginated Collection Metadata**
  - **Validates: Requirements 4.6**

- [x] 4. Enhance CanFilter Trait
  - Verify text search works across multiple fields
  - Ensure foreign key filters handle boolean string conversion
  - Verify date range filtering works correctly
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

- [ ]* 4.1 Write property test for text search
  - **Property 16: Text Search Across Fields**
  - **Validates: Requirements 7.2**

- [ ]* 4.2 Write property test for boolean string conversion
  - **Property 17: Boolean String Conversion**
  - **Validates: Requirements 7.5**

- [ ]* 4.3 Write property test for date range filtering
  - **Property 18: Date Range Filtering**
  - **Validates: Requirements 7.4**

- [x] 5. Enhance Global Exception Handler
  - Verify Handler returns JSON for API requests
  - Ensure all Laravel exceptions are caught and transformed
  - Add proper error codes for each exception type
  - Verify logging happens for database errors
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7_

- [ ]* 5.1 Write property test for Handler exception handling
  - **Property 19: Handler JSON Response for API Requests**
  - **Property 20: Handler ValidationException Handling**
  - **Property 21: Handler AuthenticationException Handling**
  - **Property 22: Handler ModelNotFoundException Handling**
  - **Validates: Requirements 8.1, 8.2, 8.3, 8.4**

- [x] 6. Update AddressController
  - Add ExceptionHandler, SuccessResponse, and CanFilter traits
  - Update index method to use applyFilters and collectionResponse
  - Update store method to use createdResponse
  - Update show method to use findOrFail and resourceResponse
  - Update update method to use findOrFail and updatedResponse
  - Update destroy method with relationship checking and database exception handling
  - Add getSearchableFields and getForeignKeyFilters methods
  - _Requirements: 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 10.1, 10.2, 10.3, 10.4_

- [ ]* 6.1 Write integration test for AddressController CRUD operations
  - Test index with filters
  - Test store with valid and invalid data
  - Test show with existing and non-existing addresses
  - Test update with valid and invalid data
  - Test destroy with and without relationships

- [ ]* 6.2 Write property test for backward compatibility
  - **Property 23: Backward Compatibility**
  - **Validates: Requirements 10.5**

- [x] 7. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
