# Data Model: PhotoPro Mobile Application

## Entities

### Galerie
Represents a collection of photos.
- **Attributes**:
  - `id`: `String` (UUID)
  - `photographerId`: `String` (UUID)
  - `title`: `String`
  - `description`: `String?`
  - `coverPhotoId`: `String?`
  - `isPublic`: `bool`
  - `layout`: `String`
  - `accessCode`: `String?`
  - `createdAt`: `DateTime`
- **Relationships**: Contains many `Photo` objects (fetched separately).

### Photo
Represents an individual image.
- **Attributes**:
  - `id`: `String` (UUID)
  - `title`: `String?`
  - `storageUrl`: `String`
  - `mimeType`: `String`
  - `fileSize`: `double` (in bytes)
- **Relationships**: Belongs to one or more `Galerie` objects.

### Commentaire
Represents a user comment.
- **Attributes**:
  - `id`: `String` (UUID)
  - `photoId`: `String` (UUID)
  - `authorName`: `String` (Anonymous user nickname)
  - `content`: `String`
  - `createdAt`: `DateTime`
- **Relationships**: Belongs to one `Photo`.

### PhotoProError
Standard error model for API failures.
- **Attributes**:
  - `code`: `String` (Error code like "ERR_UNAUTHORIZED")
  - `message`: `String` (User-friendly message)
  - `details`: `Map<String, dynamic>?` (Optional extra info)

### PaginatedResponse<T>
Generic wrapper for list-based API responses.
- **Attributes**:
  - `items`: `List<T>`
  - `totalCount`: `int`
  - `limit`: `int`
  - `offset`: `int`
  - `hasNext`: `bool`

## Persistence Strategy (Isar)
- `Galerie` and `Photo` entities will be decorated as `@Collection` for local caching.
- `PaginatedResponse` metadata will be stored to manage offline list views.
- Secure storage will hold access codes mapped to `gallery_id`.
