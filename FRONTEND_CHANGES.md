# Frontend

## Architecture & Design Patterns

### 1. Migrated to Angular 20 and bumped all dependencies and complete code refactoring
**What Changed:**
- Standalone components by defaults
- Zoneless change detection
- Angular Signals for data binding and state management
- Split app.component.ts into app.component.ts and app.component.html and separated components and services into their own files
**Why (Best Practice):**
- **Future-Proof**: Angular Signals are the future of Angular reactivity

---
### 2. Integrated Eslint
**What Changed:**
- Added Eslint to the project
- Implemented strict rules
**Why (Best Practice):**
- **Future-Proof**: Standardize code quality

---
### 3. Changed testing to jest instead of karma
**What Changed:**
- Added jest to the project
- Implemented some basic tests
**Why (Best Practice):**
- **Future-Proof**: Jest is much faster than karma
---

### 5. Implemented Signal-Based Reactive State Management
**What Changed:**
- Replaced RxJS `BehaviorSubject` with Angular `signal()` and `WritableSignal`
- Used `computed()` for derived state
- Removed manual subscription management
- Kept rxjs only for http requests

**Why (Best Practice):**
- **Fine-Grained Reactivity**: Signals provide more efficient change detection
- **Performance**: Only components using the signal re-render when it changes
- **Simpler Code**: No need for manual `subscribe()`/`unsubscribe()` patterns (only needed for http requests)
- **Memory Safety**: Automatic cleanup, prevents memory leaks
- **Modern Angular**: Signals are the future of Angular reactivity
- **Type Safety**: Better TypeScript inference with signals

**Example:**
```typescript
// Before (RxJS)
private currentUserSubject = new BehaviorSubject<User | null>(null);
currentUser$ = this.currentUserSubject.asObservable();

// After (Signals)
currentUser: WritableSignal<boolean> = signal<boolean>(false);
isAuthenticated: Signal<boolean> = this.currentUser.asReadonly();
```
---

### 6. Zoneless Change Detection
**What Changed:**
- Removed Zone.js dependency
- Configured application to run without zones
- Used provideZonelessChangeDetection

**Why (Best Practice):**
- **Better Performance**: No automatic change detection overhead
- **Explicit Control**: Change detection only when signals update
- **Smaller Bundle**: Zone.js is a large dependency
- **Modern Pattern**: Aligns with Angular's future direction
- **Predictable Behavior**: No hidden change detection cycles

**Files Changed:**
- `app.config.ts` - Added `provideZonelessChangeDetection()`
- Removed Zone.js from polyfills, tests and package.json

---

### 7. Modern Control Flow Syntax
**What Changed:**
- Replaced `*ngIf` with `@if`
- Replaced `*ngFor` with `@for`

**Why (Best Practice):**
- **Built-in Syntax**: No need to import directives
- **Better Performance**: Optimized by Angular compiler
- **Type Safety**: Better type inference in templates
- **Cleaner Templates**: More readable, less verbose
- **Future-Proof**: New recommended syntax in Angular 17+

**Example:**
```typescript
// Before
<div *ngIf="isLoading">Loading...</div>
<div *ngFor="let item of items">{{ item }}</div>

// After
@if (isLoading()) {
  <div>Loading...</div>
}
@for (item of items(); track item.id) {
  <div>{{ item }}</div>
}
```

**Files Changed:**
- All component templates (`.html` files)

---

### 8. Functional Route Guards
**What Changed:**
- Replaced class-based guards with functional guards
- Used `inject()` for dependency injection
- Simplified guard logic with arrow functions

**Why (Best Practice):**
- **Simpler Code**: Less boilerplate, no class overhead
- **Modern Pattern**: Recommended approach in Angular 14+
- **Functional Programming**: Easier to test and compose
- **Better Tree-Shaking**: Unused guards are removed from bundle
- **Dependency Injection**: `inject()` function is cleaner than constructor injection

**Example:**
```typescript
// Before (Class-based)
@Injectable()
export class AuthGuard implements CanActivate {
  constructor(private auth: AuthService) {}
  canActivate() { ... }
}

// After (Functional)
export const authenticatedGuard: CanActivateFn = () => {
  const auth = inject(Authentication);
  return auth.isAuthenticated();
};
```

**Files Changed:**
- `authenticated.ts` guard
- `anonymous.ts` guard

---

### 9. Functional HTTP Interceptors
**What Changed:**
- Replaced class-based interceptor with functional interceptor
- Used `inject()` for service dependencies
- Simplified error handling logic

**Why (Best Practice):**
- **Less Boilerplate**: No class declaration needed
- **Modern Pattern**: Functional interceptors are the new standard
- **Easier Testing**: Pure functions are easier to test
- **Better Composition**: Can chain multiple functional interceptors
- **Type Safety**: Better type inference with functional approach

**Files Changed:**
- `authentication.ts` interceptor

---
---

### 10. Proper Error Handling & User Feedback
**What Changed:**
- Centrelized error handling in interceptors
- Using signals to load data from services

**Why (Best Practice):**
- **User Experience**: Clear feedback on what went wrong
- **Accessibility**: Error messages are properly announced
- **Debugging**: Easier to track issues in production

---

### 11. Memory Leak Prevention
**What Changed:**
- Used `takeUntil()` pattern with `DestroyRef`
- Proper cleanup in component destruction
- Signal-based subscriptions (auto-cleanup)

**Why (Best Practice):**
- **Memory Safety**: Prevents memory leaks from unclosed subscriptions
- **Performance**: No lingering subscriptions consuming resources
- **Angular Pattern**: `DestroyRef` is the modern cleanup approach
- **Automatic with Signals**: Signals don't need manual cleanup
- **Production Stability**: Prevents memory issues in long-running apps

**Example:**
```typescript
private destroy$ = inject(DestroyRef);

ngOnInit() {
  this.service.data$
    .pipe(takeUntil(this.destroy$))
    .subscribe(data => { ... });
}
```

**Files Changed:**
- Components with RxJS subscriptions

---

### 11. Service Layer Architecture
**What Changed:**
- Created dedicated service layer for API communication
- Separated concerns: services handle HTTP, components handle UI
- Type-safe service methods with proper return types
- Centralized error handling in services

**Why (Best Practice):**
- **Separation of Concerns**: Components don't know about HTTP details
- **Reusability**: Services can be used by multiple components
- **Testability**: Services can be mocked easily
- **Single Responsibility**: Each service has one clear purpose
- **DRY Principle**: No duplicate HTTP logic across components

**Files Changed:**
- `authentication.ts` service
- `message.ts` service

---

### 12. Route Configuration with Lazy Loading
**What Changed:**
- Configured routes with proper lazy loading
- Used route guards for access control
- Proper redirect logic for authenticated/unauthenticated users

**Why (Best Practice):**
- **Performance**: Only load code when needed
- **Security**: Guards prevent unauthorized access
- **User Experience**: Automatic redirects to appropriate pages
- **Bundle Optimization**: Smaller initial bundle size
- **Scalability**: Easy to add new routes without affecting existing ones

**Files Changed:**
- `app.routes.ts`

---

### 13. Environment Configuration
**What Changed:**
- Proper environment files for different builds
- API URL configuration externalized
- Type-safe environment objects

**Why (Best Practice):**
- **Configuration Management**: Different settings for dev/prod
- **Security**: No hardcoded URLs in components
- **Flexibility**: Easy to change API endpoints
- **Build Optimization**: Different builds for different environments
- **12-Factor App**: Configuration separate from code

**Files Changed:**
- `environment.ts`
- `environment.production.ts`

---

### 14. Comprehensive Testing
**What Changed:**
- Unit tests for some services
- Component tests with proper mocking
- Guard and interceptor tests
- Jest timer mocks for async operations (zoneless compatible)

**Why (Best Practice):**
- **Code Quality**: Tests catch bugs early
- **Refactoring Safety**: Tests ensure changes don't break functionality
- **Documentation**: Tests show how code should be used
- **Confidence**: Deploy with confidence knowing tests pass

---

## Summary of Best Practices Applied

1. ✅ **Modern Angular Patterns**: Standalone components, signals, functional guards
2. ✅ **Performance**: Zoneless change detection, lazy loading, fine-grained reactivity
3. ✅ **Type Safety**: TypeScript strict mode, typed forms, typed services
4. ✅ **Clean Architecture**: Service layer, separation of concerns, SOLID principles
5. ✅ **Maintainability**: Clear code structure, proper error handling, comprehensive tests
6. ✅ **User Experience**: Loading states, error messages, responsive design
7. ✅ **Security**: Route guards, JWT interceptor, proper authentication flow
8. ✅ **Scalability**: Modular structure, lazy loading, reusable services
9. ✅ **Developer Experience**: Modern syntax, better tooling, clear patterns
10. ✅ **Future-Proof**: Using Angular's recommended modern approaches