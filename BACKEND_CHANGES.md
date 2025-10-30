# Backend

## Architecture & Design Patterns

### 1. Clean Architecture (Layered Architecture Pattern)
**What Changed:**
- Organized code into distinct layers: Controllers → Services → Repositories → Entities
- Clear separation of concerns with each layer having a single responsibility
- Dependencies flow inward (Controllers depend on Services, Services depend on Repositories)

**Why (Best Practice):**
- **Maintainability**: Easy to locate and modify code
- **Testability**: Each layer can be tested independently
- **Scalability**: Easy to add new features without affecting existing code
- **SOLID Principles**: Follows Single Responsibility and Dependency Inversion

**Structure:**
```
src/
├── Controller/Api/          # HTTP layer (handles requests/responses)
├── Service/                 # Business logic layer
├── Repository/              # Data access layer
├── Entity/                  # Domain models
├── DTO/                     # Data Transfer Objects
└── Chatbot/                 # Plugin system
```

---

### 2. Data Transfer Objects (DTO Pattern)
**What Changed:**
- Created dedicated DTOs for request validation
- `RegisterUserDTO` for user registration
- `CreateMessageDTO` for message creation
- Used Symfony's `#[MapRequestPayload]` attribute

**Why (Best Practice):**
- **Validation**: Centralized validation rules in one place
- **Type Safety**: Strong typing for incoming data
- **Decoupling**: Controllers don't directly work with entities
- **Security**: Only allowed fields can be set
- **Documentation**: Clear contract for API consumers

**Files:**
- `RegisterUserDTO.php` - Email, password, name validation
- `CreateMessageDTO.php` - Message content validation

---

### 4. Service Layer Pattern
**What Changed:**
- Created dedicated service classes for business logic
- `AuthenticationService` - User registration and management
- `MessageService` - Message handling and chatbot integration
- `ChatbotPluginManager` - Plugin coordination

**Why (Best Practice):**
- **Single Responsibility**: Each service has one clear purpose
- **Reusability**: Services can be used by multiple controllers
- **Testability**: Business logic isolated from HTTP layer
- **Transaction Management**: Services handle database transactions
- **Business Rules**: All business logic centralized

**Files:**
- `AuthenticationService.php`
- `MessageService.php`
- `ChatbotPluginManager.php`

---

### 5. Plugin Architecture Pattern (Strategy Pattern)
**What Changed:**
- Created `AbstractChatbotPlugin` base class
- Implemented `GenericChatbotPlugin` as example
- Used Symfony's service tagging for auto-discovery
- `ChatbotPluginManager` coordinates plugin execution

**Why (Best Practice):**
- **Extensibility**: Easy to add new chatbot behaviors without modifying existing code
- **Open/Closed Principle**: Open for extension, closed for modification
- **Loose Coupling**: Plugins are independent of each other
- **Strategy Pattern**: Different algorithms (plugins) can be swapped at runtime
- **Dependency Injection**: Plugins auto-discovered via service container

**How It Works:**
1. Plugin implements `supports()` to check if it can handle a message
2. Plugin implements `process()` and `createBotResponse()` to create bot reply
3. Manager iterates through plugins by priority
4. A message can be processed by multiple plugins

**Files:**
- `AbstractChatbotPlugin.php` - Base class with common functionality
- `GenericChatbotPlugin.php` - Example implementation
- `ChatbotPluginManager.php` - Plugin coordinator

---

### 6. JWT Authentication (Token-Based Authentication Pattern)
**What Changed:**
- Implemented JWT authentication with LexikJWTAuthenticationBundle
- Stateless authentication (no server-side sessions)
- Token-based authorization for protected endpoints

**Why (Best Practice):**
- **Scalability**: No server-side session storage needed
- **Stateless**: Each request is independent
- **Cross-Domain**: Works across different domains/services
- **Mobile-Friendly**: Easy to use in mobile apps
- **Standard**: Industry-standard authentication method

**Configuration:**
- Private/public key pair for token signing
- 1-hour token expiration
- Automatic token validation on protected routes

---

### 7. Validation with Constraints (Validation Pattern)
**What Changed:**
- Used Symfony's validation constraints on DTOs
- Email format validation
- Password strength validation (regex pattern)
- Length constraints on all fields

**Why (Best Practice):**
- **Data Integrity**: Ensures only valid data enters the system
- **Security**: Prevents injection attacks and weak passwords
- **User Experience**: Clear error messages for invalid input
- **Declarative**: Validation rules are clear and readable
- **Reusable**: Constraints can be reused across entities

**Validation Rules:**
- Email: Valid email format
- Password: Min 8 chars, uppercase, lowercase, digit, special char
- Name: Min 2 chars, max 255 chars
- Message: Min 1 char, max 5000 chars

---

### 8. Error Handling Pattern
**What Changed:**
- Proper HTTP status codes for different errors
- Structured error responses
- Validation error details with field-level messages
- Consistent error format across all endpoints

**Why (Best Practice):**
- **User Experience**: Clear error messages help users fix issues
- **API Standards**: RESTful error handling
- **Debugging**: Easier to track down issues
- **Documentation**: Errors are self-documenting
- **Client Integration**: Clients can handle errors programmatically

**Error Codes:**
- `400 Bad Request` - Validation errors
- `401 Unauthorized` - Invalid credentials or missing token
- `409 Conflict` - Resource already exists (duplicate email)
- `500 Internal Server Error` - Unexpected errors

---

### 9. Self-Referencing Entity Pattern (Composite Pattern)
**What Changed:**
- `Message` entity has self-referencing relationship
- `inReplyTo` field links to parent message
- `replies` collection contains child messages
- Supports unlimited nesting depth

**Why (Best Practice):**
- **Threading**: Natural way to represent message conversations
- **Flexibility**: Supports any depth of replies
- **Query Efficiency**: Can fetch entire thread with one query
- **Data Integrity**: Foreign key constraints ensure data consistency
- **Composite Pattern**: Messages can contain other messages

**Structure:**
```
Message 1 (User)
  └─ Message 2 (Bot) - inReplyTo: 1
      └─ Message 3 (User) - inReplyTo: 2
```
---

### 10. Comprehensive Testing (Test Pyramid Pattern)
**What Changed:**
- Unit tests for services and plugins
- Integration tests for repositories
- Functional tests for API endpoints
- Separate test database configuration

**Why (Best Practice):**
- **Quality Assurance**: Tests catch bugs before production
- **Refactoring Safety**: Can refactor with confidence
- **Documentation**: Tests show how code should be used
- **Regression Prevention**: Prevents old bugs from returning
- **Test Pyramid**: More unit tests, fewer integration/functional tests

**Test Structure:**
```
tests/
├── Unit/              # Fast, isolated tests (services, plugins)
```

---

### 11. RESTful API Design
**What Changed:**
- Resource-based URLs (`/api/auth`, `/api/messages`)
- Proper HTTP methods (GET, POST)
- Correct status codes (200, 201, 400, 401, 409)
- JSON request/response format
- Consistent endpoint naming

**Why (Best Practice):**
- **Standards Compliance**: Follows REST principles
- **Predictability**: Developers know what to expect
- **Cacheability**: GET requests can be cached
- **Stateless**: Each request contains all needed information
- **Scalability**: RESTful APIs scale well

**Endpoints:**
- `POST /api/auth/register` - Create resource (201)
- `POST /api/auth/login` - Action endpoint (200)
- `GET /api/auth/me` - Read resource (200)
- `GET /api/messages` - List resources (200)
- `POST /api/messages` - Create resource (201)

---

### 12. CORS Configuration (Security Pattern)
**What Changed:**
- Configured CORS to allow frontend access
- Whitelist specific origins
- Allow credentials (cookies, authorization headers)
- Proper preflight handling

**Why (Best Practice):**
- **Security**: Prevents unauthorized cross-origin requests
- **Flexibility**: Frontend can run on different domain
- **Standards**: Follows CORS specification
- **Development**: Easy local development with different ports
- **Production**: Can restrict to specific domains

---

## OpenAPI Documentation Changes

### What Changed in openapi.yaml

#### 1. Complete API Specification
**Before:** Basic, incomplete specification with only 2 endpoints
**After:** Complete OpenAPI 3.0 specification with all 7 endpoints

**Why:**
- **Documentation**: Developers know exactly how to use the API
- **Client Generation**: Can auto-generate client SDKs
- **Testing**: Can use tools like Postman to test API
- **Standards**: OpenAPI is the industry standard
- **Interactive**: Swagger UI provides interactive documentation

#### 2. Authentication Security Scheme
**Added:**
```yaml
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
```

**Why:**
- **Security Documentation**: Shows authentication is required
- **Client Integration**: Clients know to send Bearer token
- **Testing**: Swagger UI can send authenticated requests
- **Standards**: JWT Bearer is standard authentication method

#### 3. Request/Response Schemas
**Added:**
- Complete request body schemas with validation rules
- Response schemas with all fields and types
- Examples for all requests and responses
- Error response schemas

**Why:**
- **Type Safety**: Clients know exact data structure
- **Validation**: Shows what validation rules apply
- **Examples**: Developers can copy-paste examples
- **Contract**: Clear API contract between frontend/backend

#### 4. Endpoint Documentation
**Added for each endpoint:**
- Summary and description
- Request body schema (for POST)
- All possible response codes (200, 201, 400, 401, 409)
- Security requirements (public vs protected)
- Tags for grouping (Authentication, Messages)

**Why:**
- **Completeness**: Every endpoint fully documented
- **Error Handling**: Clients know what errors to expect
- **Grouping**: Related endpoints grouped together
- **Discoverability**: Easy to find relevant endpoints

#### 5. Validation Rules in Schema
**Added:**
- `minLength`, `maxLength` for strings
- `format: email` for email fields
- `format: password` for password fields
- `required` fields marked
- `enum` for status values
- `nullable` for optional fields

**Why:**
- **Client Validation**: Clients can validate before sending
- **Documentation**: Clear rules for data format
- **Type Safety**: Prevents invalid data
- **User Experience**: Better error messages

#### 6. Nested Object Support
**Added:**
- Recursive message schema for replies
- Self-referencing with `$ref`
- Array of messages with proper typing

**Why:**
- **Complex Data**: Supports threaded conversations
- **Reusability**: Schema defined once, referenced multiple times
- **Type Safety**: Nested objects properly typed
- **Documentation**: Shows full data structure

#### 7. Server Configuration
**Added:**
```yaml
servers:
  - url: http://localhost:8000/api
    description: Local development server
```

**Why:**
- **Base URL**: Clients know where to send requests
- **Environment**: Can have different servers for dev/prod
- **Testing**: Swagger UI uses correct base URL

#### 8. Tags for Organization
**Added:**
```yaml
tags:
  - name: Authentication
    description: User authentication and registration
  - name: Messages
    description: Message management
```

**Why:**
- **Organization**: Endpoints grouped logically
- **Navigation**: Easier to find endpoints in Swagger UI
- **Documentation**: Clear separation of concerns

---

## NelmioApiDocBundle Integration

### What Changed:
- Added OpenAPI attributes to all controllers
- Configured NelmioApiDocBundle for auto-generation
- Exposed documentation at `/api/doc/ui`, `/api/doc.json`, `/api/doc.yaml`

**Why:**
- **Auto-Generation**: Documentation stays in sync with code
- **Single Source of Truth**: Code is the documentation
- **Interactive**: Swagger UI for testing
- **Multiple Formats**: JSON and YAML for different tools
- **Maintainability**: No separate documentation to maintain

**Attributes Used:**
- `#[OA\Tag]` - Group endpoints
- `#[OA\Post]`, `#[OA\Get]` - HTTP methods
- `#[OA\RequestBody]` - Request schemas
- `#[OA\Response]` - Response schemas
- `#[OA\Property]` - Field definitions
- `security: []` - Public endpoints
- `security: [['bearerAuth' => []]]` - Protected endpoints

---

## Summary of Design Patterns Used

1. ✅ **Clean Architecture** - Layered structure (Controller → Service → Repository → Entity)
2. ✅ **Repository Pattern** - Abstracted data access
3. ✅ **Service Layer Pattern** - Business logic separation
4. ✅ **Plugin Architecture** - Extensible chatbot system (Strategy Pattern)
5. ✅ **Dependency Injection** - Constructor injection, autowiring
6. ✅ **DTO Pattern** - Request validation and data transfer
7. ✅ **Factory Pattern** - Entity creation in services
8. ✅ **Composite Pattern** - Self-referencing messages
9. ✅ **Singleton Pattern** - Services are singletons in container
10. ✅ **Observer Pattern** - Doctrine lifecycle events
11. ✅ **Template Method Pattern** - AbstractChatbotPlugin base class
12. ✅ **Facade Pattern** - Services simplify complex operations
13. ✅ **Proxy Pattern** - Doctrine lazy loading
14. ✅ **Decorator Pattern** - Symfony's event system

---

## SOLID Principles Applied

- **S**ingle Responsibility - Each class has one clear purpose
- **O**pen/Closed - Plugin system open for extension, closed for modification
- **L**iskov Substitution - Plugins can be substituted without breaking code
- **I**nterface Segregation - Small, focused interfaces
- **D**ependency Inversion - Depend on abstractions (interfaces), not concrete classes
