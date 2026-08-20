---
name: laravel-security
description: >-
  Security standards and auditing for Laravel applications. Covers CSRF, SQL Injection, XSS escaping, Mass Assignment, Rate Limiting, Authentication, and safe File Uploads.
---

# Laravel Security Best Practices & Audit Guide

This skill outlines crucial security standards to adhere to in Laravel projects.

## 1. Injection & Data Integrity
- **SQL Injection Prevention**: Always use Eloquent ORM or parameterized Query Builder bindings (`where('column', $param)`). NEVER concatenate raw SQL strings with user input.
- **Mass Assignment Vulnerabilities**: Always specify `protected $fillable = [...]` on Eloquent models, or use `$guarded = ['id']`. Never pass raw `$request->all()` into `Model::create()` without FormRequest validation.

## 2. Cross-Site Request Forgery (CSRF) & XSS
- **CSRF Protection**: All `POST`, `PUT`, `PATCH`, and `DELETE` requests in Blade must include the `@csrf` directive.
- **XSS Escaping**: Use `{{ $variable }}` which automatically escapes HTML via `htmlspecialchars()`. Avoid `{!! $variable !!}` unless the HTML is explicitly sanitized with an approved HTML purifier.

## 3. Safe File Uploads & Validation
- Validate MIME types, extensions, and file sizes:
  ```php
  'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
  ```
- Store files using unique hashes (`$file->store('products', 'public')`), never preserving original un-sanitized client filenames.

## 4. Rate Limiting & Authentication Security
- Apply Rate Limiting on authentication and checkout routes: `throttle:6,1` (6 requests per minute).
- Hash passwords exclusively with `Hash::make()` or bcrypt.
