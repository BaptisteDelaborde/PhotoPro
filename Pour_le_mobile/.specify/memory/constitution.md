span

# PhotoPro Constitution

## Core Principles

### I. Anonymous Viewer Focus

The mobile application (`app-mobile`) is dedicated exclusively to anonymous viewers. No authentication, account creation, or administrative features (such as image uploads or gallery management) shall be implemented. The user experience MUST remain frictionless and open.

### II. Flutter Cross-Platform Delivery

The application MUST be developed using the Flutter framework to ensure a consistent, high-performance experience on both iOS and Android platforms from a single codebase. Native-like performance and platform-specific UI nuances SHOULD be respected.

### III. Code-Based Private Access

Access to private galleries is strictly controlled via secret access codes provided by photographers. This mechanism MUST be the sole method for accessing restricted content, intentionally bypassing traditional account-based security for the mobile client.

### IV. Backend Domain Alignment

Mobile data models and business logic MUST strictly mirror the existing PHP backend domain entities (specifically `Galerie` and `Photo`). Consistency in naming conventions and data structures across the stack is non-negotiable to ensure long-term maintainability.

### V. Interaction Integrity (Comments)

The only permissible user interaction involving data persistence in the mobile app is adding comments to photos within private galleries. This feature MUST strictly adhere to the existing backend API contracts and validation rules.

## Technical Constraints

- **Framework**: Flutter (Dart) for the `app-mobile` directory.
- **Platforms**: Support for iOS (latest 2 versions) and Android (API 26+).
- **Communication**: Must interact with the `gateway-frontoffice` microservice.
- **Scope**: STRICTLY restricted to public browsing, private code access, and commenting.

## Development Quality

- **Architecture**: Adoption of a clear state management pattern (e.g., BLoC, Provider) to decouple UI from business logic.
- **Performance**: High-performance image loading and caching are mandatory to ensure a smooth gallery browsing experience.
- **Resilience**: The app MUST handle offline states and API errors gracefully with user-friendly feedback.

## Governance

- **Supremacy**: This constitution supersedes all other informal practices. Any deviation must be justified and documented.
- **Amendments**: Amendments require a version bump. MAJOR for scope changes, MINOR for new principles, PATCH for clarifications.
- **Compliance**: All pull requests and specifications MUST be verified against these principles.

**Version**: 1.0.0 | **Ratified**: 2026-04-02 | **Last Amended**: 2026-04-02
