# Research: Mobile Application Data Layer

## Decisions

### 1. Storage Choice (Caching & Persistence)
- **Decision**: Use `Isar` for gallery and photo caching; `flutter_secure_storage` for access codes.
- **Rationale**: `Isar` is highly performant, type-safe, and supports ACID transactions. It is superior to `SharedPreferences` for complex data lists and more modern than `Hive`. Secure storage is essential for maintaining access tokens or codes that should not be in plain text.
- **Alternatives considered**: `SharedPreferences` (rejected as too simple for gallery lists), `Hive` (rejected in favor of Isar's performance and query capabilities).

### 2. Dependency Injection Pattern
- **Decision**: Use `Riverpod` providers for DI.
- **Rationale**: Riverpod naturally handles DI in a testable way without the boilerplate of `get_it`. It integrates seamlessly with Flutter and manages object lifecycles efficiently.
- **Alternatives considered**: `GetIt` (rejected as it's a service locator and more global than scoped providers).

### 3. API Communication (Dio Configuration)
- **Decision**: Global Dio instance with a `PhotoProInterceptor`.
- **Rationale**: Interceptors provide a centralized place to handle headers, logging, and mapping HTTP errors (403, 429, 500) to our `PhotoProError` domain model. This ensures consistency across all API calls.

## Research Tasks Summary
- [x] Research Isar vs Hive for caching (Decision: Isar)
- [x] Research Dio interceptor patterns for error mapping (Decision: Global interceptor)
- [x] Resolve "Local Storage" unknown in Technical Context
