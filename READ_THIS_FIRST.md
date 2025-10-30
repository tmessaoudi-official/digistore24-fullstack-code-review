# Important Design Decision

## Why No API Platform?

The choice to **not use API Platform** (or similar frameworks like FOSRestBundle) was **intentional and strategic**.
And even though the test was about the backend, i did fix the frontend to follow angular best practices and to work with the API i built.

### Reasoning:
**5. API Platform comparaison**
 - Using API Platform would have given automatic CRUD endpoints
 - Using API Platform would have given built-in pagination, filtering, sorting
 - Using API Platform would have given automatic OpenAPI generation
 - Using API Platform would have given GraphQL support out of the box
 - The Chatbot plugin system would be implemented using a State Processor
 - Basically the same as the manual implementation, but with less code and it would mean that i understand how API Platform works
 - In a real life example (not this test) API platform would obviously give you a lot of advantages with less code so you can ship secure/scalable API faster

**1. Showcase Core Skills**
- This test is meant to demonstrate my understanding of fundamental concepts
- Using API Platform would abstract away the architecture I wanted to showcase
- Building from scratch proves I understand the underlying principles, not just how to configure a bundle

**2. Clean Architecture by Hand**
- Manually implemented Controller → Service → Repository → Entity layers
- Shows understanding of separation of concerns and SOLID principles
- Demonstrates ability to structure code without framework magic

**3. Minimal Dependencies**
- Kept the dependency list lean and purposeful
- Every dependency serves a clear, specific purpose
- Easier to maintain and understand the codebase

**4. Learning & Flexibility**
- API Platform is opinionated and can be restrictive
- Manual implementation gives full control over every aspect
- Shows I can adapt to any architecture, not just API Platform's way

### What I Built Instead:

✅ **RESTful API** - Proper HTTP methods, status codes, resource-based URLs  
✅ **OpenAPI Documentation** - Complete specification with NelmioApiDocBundle  
✅ **Validation** - DTOs with Symfony constraints  
✅ **Serialization** - Custom `toArray()` methods for full control  
✅ **Error Handling** - Consistent error responses across all endpoints  
✅ **Authentication** - JWT with LexikJWTAuthenticationBundle  
✅ **Testing** - Unit, integration, and functional tests  

### Trade-offs Acknowledged:

**What API Platform Would Have Given:**
- Automatic CRUD endpoints
- Built-in pagination, filtering, sorting
- Automatic OpenAPI generation
- GraphQL support out of the box

**What I Gained Instead:**
- Deep understanding of every layer
- Full control over implementation
- Cleaner, more maintainable code for this use case
- Demonstration of architectural skills

### Conclusion:

This was a **conscious architectural decision** to demonstrate:
- Understanding of clean architecture principles
- Ability to build production-ready APIs without heavy frameworks
- Knowledge of design patterns and best practices
- Skill in making pragmatic technology choices

For a production application at scale, API Platform might be the right choice. For this test, building from scratch better showcases the skills being evaluated.
