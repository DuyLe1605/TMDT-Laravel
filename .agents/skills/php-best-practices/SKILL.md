---
name: php-best-practices
description: >-
  Advanced PHP 8.2/8.3+ programming standards, strict typing, SOLID principles, PSR-12 code style, modern error handling, and clean code conventions.
---

# Modern PHP Best Practices & Coding Standards

This skill defines the coding standards for writing clean, type-safe, and maintainable PHP 8.2+ code.

## 1. Type Safety & Strict Typing
- **Declare Strict Types**: When appropriate, use strict typing conventions.
- **Type Hint Everything**:
  - Property types: `protected CategoryService $categoryService;`
  - Parameter types: `public function createCategory(array $data): Category`
  - Return types: Always specify explicit return types (`void`, `bool`, `int`, `string`, `Collection`, `LengthAwarePaginator`, `View`, `RedirectResponse`).
- **Constructor Property Promotion**: Use PHP 8 constructor promotion to simplify class dependencies:
  ```php
  public function __construct(
      protected CategoryService $categoryService,
      protected ProductService $productService,
  ) {}
  ```

## 2. Eliminating Magic Numbers & Strings
- Never write hardcoded numbers or strings in business logic.
- Use `final class AppConstants` or PHP 8.1+ Enums for statuses, roles, limits, and system messages.

## 3. Error Handling & Exception Management
- Throw domain-specific exceptions instead of generic `\Exception`.
- Handle expected errors gracefully with meaningful user feedback.

## 4. PSR-12 Code Formatting
- 4 spaces indentation.
- Opening braces for classes and methods on the next line.
- CamelCase for methods and variables (`getCategoryList`), PascalCase for classes and interfaces (`CategoryService`), UPPER_SNAKE_CASE for constants (`DEFAULT_PAGINATION_LIMIT`).
