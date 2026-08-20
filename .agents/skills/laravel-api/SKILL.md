---
name: laravel-api
description: >-
  Standards for designing RESTful JSON APIs in Laravel for eCommerce applications. Covers API Resource transformations, HTTP status codes, structured JSON error responses, and pagination.
---

# Laravel RESTful API Architecture Standards

This skill provides patterns for building clean, consistent, and performant REST APIs.

## 1. Response Standards & HTTP Status Codes
Always return standardized JSON envelopes:
- **`200 OK`**: Successful GET, PUT, PATCH request (`['success' => true, 'data' => ...]`).
- **`201 Created`**: Successful POST resource creation (`['success' => true, 'message' => '...', 'data' => ...]`).
- **`204 No Content`**: Successful DELETE request.
- **`400 Bad Request`**: Client-side logical error.
- **`401 Unauthorized`**: Authentication missing or invalid token.
- **`403 Forbidden`**: Authenticated user lacks permission.
- **`404 Not Found`**: Resource does not exist.
- **`422 Unprocessable Entity`**: Validation failures (`['success' => false, 'errors' => [...]]`).

## 2. API Resources & Data Transformation
- Never return raw Eloquent Models directly from API controllers.
- Use Laravel API Resources (`php artisan make:resource CategoryResource`):
  ```php
  public function toArray(Request $request): array
  {
      return [
          'id' => $this->id,
          'name' => $this->name,
          'created_at' => $this->created_at?->toISOString(),
      ];
  }
  ```

## 3. Pagination & Filtering
- Wrap paginated queries using Resource Collections: `CategoryResource::collection($paginatedCategories)`.
