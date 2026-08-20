---
name: laravel-crud-generator
description: >-
  Chuyên gia tự động hóa khởi tạo module CRUD chuẩn kiến trúc Enterprise cho Laravel (Model, Migration, FormRequest, Service Layer, Controller, Blade Views, Routes, Seeder).
---

# Laravel CRUD Module Generator Skill

Kỹ năng này hướng dẫn quy trình tạo một module CRUD mới cho dự án TMDT theo đúng tiêu chuẩn kiến trúc hiện đại đã thiết lập.

## Quy Trình 7 Bước Triển Khai Module

Khi người dùng yêu cầu tạo mới một module (ví dụ: `Product`, `Order`, `User`):

### Bước 1: Khởi tạo Migration & Model
1. Chạy lệnh: `php artisan make:model <ModelName> -m`
2. Cập nhật migration trong `database/migrations/`:
   - Khai báo đầy đủ các trường, khóa ngoại (foreign key), ràng buộc `onDelete('cascade')`.
3. Cập nhật Model trong `app/Models/<ModelName>.php`:
   - Khai báo `protected $fillable = [...]`
   - Khai báo quan hệ Eloquent (`belongsTo`, `hasMany`...)
   - Khai báo `$casts` nếu có trường số thực, boolean hoặc JSON.

### Bước 2: Tạo Form Request Validation
1. Tạo 2 request:
   - `app/Http/Requests/<ModelName>/Store<ModelName>Request.php`
   - `app/Http/Requests/<ModelName>/Update<ModelName>Request.php`
2. Sử dụng hằng số từ `App\Constants\AppConstants` để tránh magic number.
3. Viết message tiếng Việt rõ ràng, thân thiện trong phương thức `messages()`.

### Bước 3: Tạo Service Layer
1. Tạo file: `app/Services/<ModelName>Service.php`
2. Định nghĩa các phương thức nghiệp vụ:
   - `getPaginated<Plural>()`
   - `create<ModelName>(array $data)`
   - `update<ModelName>(<ModelName> $item, array $data)`
   - `delete<ModelName>(<ModelName> $item)`

### Bước 4: Tạo Controller Mỏng (Thin Controller)
1. Tạo file: `app/Http/Controllers/<ModelName>Controller.php`
2. Inject `<ModelName>Service` qua Constructor.
3. Đầy đủ Type Hinting (`View`, `RedirectResponse`).
4. Sử dụng hằng số flash message từ `AppConstants`.

### Bước 5: Đăng ký Route
1. Mở `routes/web.php` và thêm:
   ```php
   use App\Http\Controllers\<ModelName>Controller;
   Route::resource('<resource_slug>', <ModelName>Controller::class);
   ```

### Bước 6: Xây Dựng Giao Diện Blade Views
Tạo thư mục `resources/views/<resource_slug>/` gồm 4 file:
1. `index.blade.php`: Table hiển thị danh sách, phân trang `$items->links()`, badge ID, nút thao tác.
2. `create.blade.php`: Form thêm mới với `old(...)`, class `form-control-custom`, hiển thị lỗi validate.
3. `edit.blade.php`: Form chỉnh sửa với `@method('PUT')`.
4. `show.blade.php`: Trang xem chi tiết thông tin.
*Tất cả view đều kế thừa `@extends('layouts.app')` và sử dụng `<x-navbar />`, `<x-alert />`.*

### Bước 7: Tạo Seeder & Kiểm thử
1. Tạo seeder: `php artisan make:seeder <ModelName>Seeder`
2. Nạp dữ liệu mẫu vào `database/seeders/DatabaseSeeder.php`.
3. Chạy `php artisan migrate --seed` để kiểm tra.
