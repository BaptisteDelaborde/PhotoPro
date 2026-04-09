# API Contract: PhotoPro Mobile Client

## Overview
The mobile application communicates with the `gateway-frontoffice` microservice using JSON over HTTP.

## Authentication
None required (Anonymous scope).

## Endpoints

### 1. GET /galeries (Public List)
- **Response (200 OK)**: `PaginatedResponse<Galerie>`
- **Errors**:
  - `500 Internal Server Error`: `PhotoProError`

### 2. GET /galeries/code/{code} (Private Access)
- **Parameters**: `code` (String, 6 digits)
- **Response (200 OK)**: `Galerie` object.
- **Errors**:
  - `403 Forbidden`: `PhotoProError` (Invalid or expired code)
  - `404 Not Found`: `PhotoProError` (Gallery not found)

### 3. GET /galeries/{id}/photos (Gallery Details)
- **Response (200 OK)**: `PaginatedResponse<Photo>`
- **Errors**:
  - `404 Not Found`: `PhotoProError`

### 4. POST /galeries/{id}/photos/{photoId}/commentaires (Add Comment)
- **Body**:
  ```json
  {
    "author_name": "nickname",
    "content": "comment text"
  }
  ```
- **Response (201 Created)**: `Commentaire` object.
- **Errors**:
  - `400 Bad Request`: `PhotoProError` (Validation failure)
  - `429 Too Many Requests`: `PhotoProError` (Rate limit)
