---
name: laravel-best-practices
description: >-
  Comprehensive guide and standards for modern Laravel development (Laravel 11/12/13). Covers thin controllers, FormRequests, Eloquent optimization, N+1 query prevention, Service Layer, and security conventions.
---

# Laravel Best Practices & Architecture Standards

This skill provides expert conventions and architectural guidelines for building robust, clean, and maintainable Laravel applications.

## 1. Controllers & Request Lifecycle
- **Keep Controllers Thin**: Controllers must only receive incoming HTTP requests, delegate business logic to Dedicated Services, and return HTTP responses (`View` or `JsonResponse` / `RedirectResponse`).
- **Always Use FormRequests**: Never validate requests inline with `$request->validate()` for complex CRUD operations. Use `app/Http/Requests/{Module}/Store{Module}Request.php` and `Update{Module}Request.php`.
- **Use Dependency Injection**: Inject services and dependencies through the constructor.
- **Route Model Binding**: Leverage implicit Route Model Binding (`public function show(Category $category)`) instead of manual `findOrFail($id)` where applicable.

## 2. Eloquent ORM & Query Optimization
- **Prevent N+1 Query Problem**: Always eager-load relationships using `with(['relation1', 'relation2'])`.
- **Select Specific Columns**: For large tables, select only required fields with `select(['id', 'name', 'price'])`.
- **Database Indexing**: Ensure columns used in `WHERE`, `ORDER BY`, or `JOIN` conditions have database indexes in their migrations.
- **Mass Assignment Protection**: Explicitly declare `protected $fillable = [...]` on all Eloquent models.

## 3. Service Layer & Business Logic
- Extract complex creation, update, and calculation logic into dedicated service classes in `app/Services/`.
- Use Database Transactions (`DB::transaction(function () { ... })`) when executing multiple related database write operations.

## 4. Blade Views & UI Structure
- Structure views systematically: `resources/views/{resource}/{index,create,edit,show}.blade.php`.
- Extract reusable components into `resources/views/components/` and invoke them with `<x-component-name />`.
- Never write extensive inline CSS in Blade templates; maintain dedicated style sheets in `public/css/`.
- Use CSRF tokens (`@csrf`) on all POST/PUT/DELETE forms and specify `@method('PUT')` or `@method('DELETE')`.
