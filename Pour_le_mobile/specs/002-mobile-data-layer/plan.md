# Implementation Plan: Mobile Application Data Layer

**Branch**: `002-mobile-data-layer` | **Date**: 2026-04-02 | **Spec**: [specs/002-mobile-data-layer/spec.md](spec.md)
**Input**: Feature specification from `/specs/002-mobile-data-layer/spec.md`

## Summary

This feature involves setting up the foundation and data layer for the PhotoPro mobile application within the `app-mobile` directory. The approach follows Clean Architecture principles, separating the UI, Domain, and Data layers. Key components include Flutter models (Galerie, Photo, Commentaire) with JSON serialization using Freezed and json_serializable, an API service layer using Dio for communicating with the Gateway, and state management via Riverpod.

## Technical Context

**Language/Version**: Dart 3.x / Flutter 3.x
**Primary Dependencies**: Flutter, Riverpod (state management), Dio (HTTP client), Freezed (data modeling), json_serializable (JSON), GoRouter (routing)
**Storage**: Isar (for gallery caching) and Flutter Secure Storage (for access codes)
**Testing**: flutter_test (Unit & Widget tests), Mocktail/Mockito (Mocking)
**Target Platform**: iOS 13+, Android API 26+
**Project Type**: mobile-app
**Performance Goals**: App boot to interactive < 2s, instantaneous navigation
**Constraints**: Clean Architecture required, STRICTLY anonymous viewer scope (no auth)
**Scale/Scope**: ~10 screens, public and private gallery browsing, photo commenting

## Constitution Check

| Principle | Status | Implementation in this Feature |
|-----------|--------|--------------------------------|
| I. Anonymous Viewer Focus | ✅ PASS | No auth/login features included. Mobile client is read-only except for comments. |
| II. Flutter Cross-Platform | ✅ PASS | Using Flutter framework for `app-mobile`. |
| III. Code-Based Private Access | ✅ PASS | API service includes methods for validation via access codes. |
| IV. Backend Domain Alignment | ✅ PASS | Flutter models mirror SQL schema/PHP entities (Galerie, Photo, Comment). |
| V. Interaction Integrity | ✅ PASS | Only "write" operation permitted is adding comments. |

## Project Structure

### Documentation (this feature)

```text
specs/002-mobile-data-layer/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output
```

### Source Code (repository root)

```text
app-mobile/
├── lib/
│   ├── main.dart
│   ├── core/
│   │   ├── api/          # Dio client, interceptors
│   │   ├── router/       # GoRouter config
│   │   └── theme/        # UI styling
│   ├── data/
│   │   ├── models/       # Freezed/json_serializable models
│   │   ├── repositories/ # Repository implementations (API + Local)
│   │   └── sources/      # Local/Remote data sources
│   ├── domain/
│   │   ├── entities/     # Domain entities (if different from data models)
│   │   └── repositories/ # Repository interfaces
│   ├── presentation/
│   │   ├── providers/    # Riverpod providers
│   │   ├── screens/      # Feature screens
│   │   └── widgets/      # Shared components
├── test/
│   ├── unit/             # Data model and repository tests
│   ├── widget/           # UI component tests
```

**Structure Decision**: Standard Flutter Clean Architecture layout within the `app-mobile` folder.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| N/A | | |
