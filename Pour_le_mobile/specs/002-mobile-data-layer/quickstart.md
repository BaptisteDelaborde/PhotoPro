# Quickstart: PhotoPro Mobile Data Layer

## Development Environment
1.  **Flutter SDK**: Ensure Flutter 3.x is installed.
2.  **Dependencies**: Run `flutter pub get` in `app-mobile`.
3.  **Code Generation**: Run `dart run build_runner watch --delete-conflicting-outputs` to generate models.

## Implementing a New Model
1.  Define the entity in `lib/data/models/`.
2.  Use the `@freezed` and `@JsonSerializable` annotations.
3.  Run the build_runner.

## API Integration
1.  Define the repository interface in `lib/domain/repositories/`.
2.  Implement the repository in `lib/data/repositories/` using the `Dio` client.
3.  Map API responses to models using `.fromJson()`.
4.  Handle errors via the global `PhotoProInterceptor`.

## Caching Data (Isar)
1.  Add the `@Collection` annotation to models in `lib/data/models/`.
2.  Update the Isar schema and run code generation.
3.  Implement cache-first or network-first logic in the repository.

## State Management (Riverpod)
1.  Create a provider in `lib/presentation/providers/`.
2.  Inject the repository into the provider.
3.  Use `ref.watch()` in widgets to consume data.
