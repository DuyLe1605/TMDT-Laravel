---
name: laravel-testing
description: >-
  Methodologies for writing automated Feature and Unit tests in Laravel. Covers Pest/PHPUnit, HTTP testing, Database assertions, and validation testing.
---

# Laravel Automated Testing Standards

This skill provides testing guidelines for eCommerce CRUD and business logic.

## 1. Feature Tests Structure
- Place HTTP and CRUD feature tests in `tests/Feature/`.
- Use the `RefreshDatabase` trait to run tests inside an isolated database sandbox.

## 2. Testing CRUD Workflows
Example test structure for CRUD modules (e.g. Category):
```php
public function test_can_list_categories(): void
{
    Category::factory()->count(3)->create();
    $response = $this->get(route('categories.index'));
    $response->assertStatus(200);
    $response->assertViewHas('categories');
}

public function test_can_create_category_with_valid_data(): void
{
    $response = $this->post(route('categories.store'), [
        'name' => 'Thời trang cao cấp',
    ]);
    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Thời trang cao cấp']);
}

public function test_cannot_create_category_with_empty_name(): void
{
    $response = $this->post(route('categories.store'), [
        'name' => '',
    ]);
    $response->assertSessionHasErrors(['name']);
}
```

## 3. Database Assertions
- `$this->assertDatabaseHas('table_name', ['column' => 'value']);`
- `$this->assertDatabaseMissing('table_name', ['column' => 'value']);`
- `$this->assertDatabaseCount('table_name', $count);`
