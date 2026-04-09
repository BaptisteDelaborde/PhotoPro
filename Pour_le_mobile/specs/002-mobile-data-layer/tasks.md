# Tasks: Mobile Application Data Layer & Foundation

**Input**: Design documents from `specs/002-mobile-data-layer/`
**Prerequisites**: plan.md (required), spec.md (required)

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story, following a Clean Architecture layered approach within each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Path Conventions: `app-mobile/lib/`, `app-mobile/test/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and Clean Architecture structure

- [x] T001 Initialize Flutter project in `app-mobile/` (ensure it exists and is valid)
- [x] T002 Configure `app-mobile/pubspec.yaml` with dependencies: `dio`, `flutter_riverpod`, `go_router`, `freezed_annotation`, `json_annotation`, `isar`, `isar_flutter_libs`, `flutter_secure_storage`, and dev_dependencies: `build_runner`, `freezed`, `json_serializable`, `isar_generator`
- [x] T003 [P] Create directory structure: `lib/core/`, `lib/data/models/`, `lib/data/repositories/`, `lib/domain/repositories/`, `lib/domain/entities/`, `lib/presentation/providers/`, `lib/presentation/screens/`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story implementation

- [x] T004 Implement `Dio` client with `PhotoProInterceptor` for error mapping (403, 404, 429, 500) and logging in `app-mobile/lib/core/api/`
- [x] T005 Setup `GoRouter` with initial shell and splash route configuration in `app-mobile/lib/core/router/router.dart`
- [x] T006 [P] Initialize `Isar` database and `FlutterSecureStorage` singleton in `app-mobile/lib/core/persistence/`

**Checkpoint**: Foundation ready - layer-specific implementation can now begin.

---

## Phase 3: User Story 1 - Public Gallery Data Retrieval (Priority: P1) 🎯 MVP

**Goal**: Fetch and display public galleries for anonymous viewers.

**Independent Test**: Launch app, see list of public galleries fetched from Gateway, verify JSON deserialization works.

### Layer 1: Data (Models & Local Persistence)
- [x] T007 [P] [US1] Create `Galerie` and `PaginatedResponse<T>` models with Freezed/JSON serialization in `app-mobile/lib/data/models/galerie.dart`

- [x] T008 [P] [US1] Create `PhotoProError` model in `app-mobile/lib/data/models/error.dart`
- [x] T009 [US1] Run `build_runner` to generate serialization code

### Layer 2: Domain & Data Implementation (Repositories)
- [x] T010 [US1] Define `GalleryRepository` interface in `app-mobile/lib/domain/repositories/gallery_repository.dart`
- [x] T011 [US1] Implement `ApiGalleryRepository` with `Dio` and `Isar` caching for `fetchPublicGalleries` in `app-mobile/lib/data/repositories/api_gallery_repository.dart`

### Layer 3: Presentation (Providers & UI)
- [x] T012 [US1] Create Riverpod `publicGalleriesProvider` in `app-mobile/lib/presentation/providers/gallery_providers.dart`
- [x] T013 [US1] Create `GalleryListScreen` with a basic `ListView` of `GalleryCard` widgets in `app-mobile/lib/presentation/screens/gallery_list_screen.dart`
- [x] T014 [US1] Register `GalleryListScreen` route in `GoRouter` configuration

**Checkpoint**: User Story 1 is functional and testable independently.

---

## Phase 4: User Story 2 - Private Gallery Access via Code (Priority: P2)

**Goal**: Access restricted galleries using a 6-digit secret code.

**Independent Test**: Enter a valid code on Access Screen, verify redirection to private gallery view, verify code is saved in secure storage.

### Layer 1 & 2: Data & Domain
- [x] T015 [US2] Add `getGalleryByCode(String code)` method to `GalleryRepository` interface and implement it in `ApiGalleryRepository`
- [x] T016 [US2] Implement `FlutterSecureStorage` logic in repository to persist access codes mapped to gallery IDs

### Layer 3: Presentation (UI)
- [x] T017 [US2] Create `AccessCodeScreen` with a 6-digit input field and validation logic in `app-mobile/lib/presentation/screens/access_code_screen.dart`
- [x] T018 [US2] Implement Riverpod `privateGalleryProvider` that takes a code as parameter
- [x] T019 [US2] Add route for `AccessCodeScreen` and logic to handle access-protected gallery navigation in `GoRouter`

---

## Phase 5: User Story 3 - Photo Commenting Persistence (Priority: P3)

**Goal**: View photos in a gallery and add anonymous comments with a nickname.

**Independent Test**: Open a photo, submit a comment with a nickname, verify the POST request payload and UI update.

### Layer 1 & 2: Data & Domain
- [x] T020 [P] [US3] Create `Photo` and `Commentaire` Freezed models in `app-mobile/lib/data/models/`
- [x] T021 [US3] Add `getGalleryPhotos(String galleryId)` and `postComment(String photoId, String nickname, String content)` to `GalleryRepository`
- [x] T022 [US3] Implement methods in `ApiGalleryRepository` (POST to `/galeries/{id}/photos/{photoId}/commentaires`)

### Layer 3: Presentation (UI)
- [x] T023 [US3] Create `GalleryDetailScreen` (Photo Grid) in `app-mobile/lib/presentation/screens/gallery_detail_screen.dart`
- [x] T024 [US3] Create `PhotoDetailScreen` with a `CommentForm` widget in `app-mobile/lib/presentation/screens/photo_detail_screen.dart`
- [x] T025 [US3] Register routes for Gallery Detail and Photo Detail in `GoRouter`

---

## Phase 6: Polish & Cross-Cutting Concerns

- [x] T026 [P] Implement a global `ErrorDialog` or `SnackBar` system triggered by the `PhotoProInterceptor`
- [x] T027 Implement "Offline Mode" indicators in the UI when data is served from Isar cache
- [x] T028 Final visual polish: Spacing, Typography, and consistent interactive feedback per PhotoPro Constitution

---

## Dependencies & Execution Order

1. **Setup (Phase 1)** -> **Foundational (Phase 2)** (Linear)
2. **Phase 2** -> **User Story 1 (Phase 3)** (Foundation blocks all stories)
3. **User Story 1 (Phase 3)** -> **User Story 2 & 3** (Common models and repository patterns established in US1)
4. **User Story 2 & 3** can be implemented in parallel if needed, but sequential order (P2 -> P3) is recommended for simplicity.

## Parallel Execution Examples

### Within User Story 1
```bash
# Models can be created in parallel:
T007 Create Galerie model
T008 Create PhotoProError model
```

### Across Stories (After Phase 2)
While not recommended for a single developer, T012 (US1 Provider) and T015 (US2 Repo methods) could theoretically be worked on in parallel by different team members once the base Repo interface exists.

---

## Implementation Strategy

1. **Foundation First**: Establish the Dio client and Routing.
2. **MVP Delivery**: Complete Phase 3 (US1) to have a working "Public Gallery" viewer.
3. **Feature Expansion**: Add Private Access (US2) then Commenting (US3).
4. **Resilience**: Finalize error handling and offline caching in Phase 6.
