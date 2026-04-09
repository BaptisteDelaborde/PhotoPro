# Feature Specification: Mobile Application Data Layer

**Feature Branch**: `002-mobile-data-layer`  
**Created**: 2026-04-02  
**Status**: Draft  
**Input**: User description: "Rédige également les spécifications pour la couche \"Data\" de l'application mobile. En te basant sur le dossier back du projet, définis les modèles de données Flutter (Galerie, Photo, Commentaire) avec leur sérialisation JSON (ex: via Freezed ou json_serializable). Spécifie également les interfaces des services API qui appelleront la Gateway pour récupérer les galeries publiques, valider un code d'accès privé, et envoyer un commentaire."

## Clarifications

### Session 2026-04-02

- Q: Should we define a standardized ApiError model to parse structured error responses? → A: Option A (Define a PhotoProError model).
- Q: Should the Galerie and Photo list endpoints support paginated responses and how should they be modeled? → A: Option B (Return a PaginatedResponse<T> object).
- Q: Should the data layer include a local persistence mechanism to cache public gallery data? → A: Option A (Implement repository-level cache).
- Q: How should the mobile app identify the author of a comment? → A: Option A (Prompt the user for a nickname).
- Q: Should the API service implementation include a centralized interceptor for handling cross-cutting communication concerns? → A: Option A (Define a global interceptor for error mapping, logging, and timeout management).

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Public Gallery Data Retrieval (Priority: P1)

As a mobile application developer, I want to use standardized data models for public galleries so that I can reliably display the list of available content to anonymous users.

**Why this priority**: Essential for the MVP "Public Browsing" feature.

**Independent Test**: Data models can be tested via unit tests by deserializing sample JSON responses from the backend.

**Acceptance Scenarios**:

1. **Given** a valid JSON response for a gallery list, **When** I deserialize it using the `Galerie` model, **Then** all fields (ID, title, cover photo ID) must be correctly populated.
2. **Given** a network request to `/galeries`, **When** the API service is called, **Then** it should return a list of `Galerie` objects.

---

### User Story 2 - Private Gallery Access via Code (Priority: P2)

As a developer, I want an interface to validate access codes so that the application can transition users from the access screen to a private gallery view.

**Why this priority**: Fulfills the "Private Access" business requirement.

**Independent Test**: Verify the API interface correctly handles both success (returning a gallery) and failure (403/404) for access codes.

**Acceptance Scenarios**:

1. **Given** a valid 6-digit access code, **When** the validate method is called, **Then** the service returns the corresponding `Galerie` object.
2. **Given** an invalid code, **When** the validate method is called, **Then** the service throws a specific "Access Denied" or "Not Found" exception.

---

### User Story 3 - Photo Commenting Persistence (Priority: P3)

As a developer, I want a standardized way to send comments to the backend along with a user-provided nickname so that user feedback is correctly persisted and identified in the database.

**Why this priority**: Adds the requested interactive feedback capability.

**Independent Test**: Mock the POST request and verify the `Comment` model is correctly serialized into the body with the provided author name.

**Acceptance Scenarios**:

1. **Given** a `Comment` object and a nickname, **When** the post method is called, **Then** it sends a POST request with the correct JSON payload (including the nickname) to the Gateway.

---

### Edge Cases

- **Missing Optional Fields**: The models must handle cases where fields like `description` or `access_code` are null in the JSON.
- **Malformed JSON**: The data layer must catch deserialization errors and throw a custom `DataParsingException`.
- **API Rate Limiting**: The API service must be prepared to handle "429 Too Many Requests" status codes.
- **Offline Mode**: When no internet connection is available, the repository MUST fallback to a `LocalDataRepository` to provide previously cached public gallery data.
- **Communication Failures**: Centrally handle timeouts (default 10s) and connectivity errors via a global interceptor.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST provide a `Galerie` model mirroring the backend `galleries` table, including support for UUID IDs and timestamp fields.
- **FR-002**: The system MUST provide a `Photo` model mirroring the backend `photos` table, including the `storage_url`.
- **FR-003**: The system MUST provide a `Commentaire` model with fields for `id`, `photo_id`, `author_name` (user-provided nickname), and `content`.
- **FR-004**: The system MUST provide a `PhotoProError` model to parse structured error responses (code, message, details) from the Gateway.
- **FR-005**: The system MUST provide a generic `PaginatedResponse<T>` model to wrap lists of entities with metadata (total count, limit, offset, hasNext).
- **FR-006**: The system MUST implement a `LocalDataRepository` interface to handle persistence of public gallery lists and metadata for offline support.
- **FR-007**: The system MUST implement a global `ApiClientInterceptor` to manage timeout policies, request/response logging, and mapping HTTP errors to domain-specific exceptions.
- **FR-008**: All data models MUST support automated JSON serialization and deserialization (e.g., camelCase to snake_case mapping).
- **FR-009**: The API service interface MUST define a method to fetch a paginated list of public galleries from the Gateway.
- **FR-010**: The API service interface MUST define a method to retrieve a specific gallery's details and a paginated list of its photos.
- **FR-011**: The API service interface MUST define a method to submit a comment for a specific photo.

### Key Entities

- **Galerie (Data Model)**:
    - `id` (String/UUID)
    - `photographer_id` (String/UUID)
    - `title` (String)
    - `description` (String?)
    - `cover_photo_id` (String?)
    - `is_public` (Bool)
    - `layout` (String)
    - `access_code` (String?)
    - `created_at` (DateTime)

- **Photo (Data Model)**:
    - `id` (String/UUID)
    - `title` (String?)
    - `storage_url` (String)
    - `mime_type` (String)
    - `file_size` (Double)

- **Commentaire (Data Model)**:
    - `id` (String/UUID)
    - `photo_id` (String/UUID)
    - `author_name` (String)
    - `content` (String)
    - `created_at` (DateTime)

- **PhotoProError (Data Model)**:
    - `code` (String)
    - `message` (String)
    - `details` (Map?)

- **PaginatedResponse<T> (Data Model)**:
    - `items` (List<T>)
    - `totalCount` (Int)
    - `limit` (Int)
    - `offset` (Int)
    - `hasNext` (Bool)

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of fields defined in the SQL schema for `galleries`, `photos`, and `comments` must be representable in the Flutter models.
- **SC-002**: JSON deserialization of a 50-item gallery list MUST take less than 100ms on a mid-range device.
- **SC-003**: API interfaces MUST support asynchronous operations (Future/Stream) to avoid blocking the UI thread.
- **SC-004**: Code generation for models (e.g., via Freezed) MUST result in zero linter warnings.

## Assumptions

- **Tooling**: The user confirmed preference for `Freezed` for models and `json_serializable` for JSON handling.
- **Gateway Endpoints**: It is assumed the Gateway exposes `/galeries`, `/galeries/code/{code}`, and `/galeries/{id}/photos/{photoId}/commentaires` as identified in the backend research.
- **Naming Conventions**: Backend uses `snake_case` (SQL/PHP), while Flutter models will use `camelCase` with appropriate `@JsonKey` mapping.
- **Mock Data**: Unit tests for the data layer will use local JSON fixtures mirroring backend responses.
