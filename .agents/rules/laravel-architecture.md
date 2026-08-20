# Quy Chuẩn Kiến Trúc Laravel Cho Dự Án TMDT

Mọi code PHP / Laravel viết trong dự án này phải tuân thủ nghiêm ngặt các quy tắc sau:

## 1. Nguyên Tắc Cốt Lõi (Architecture Principles)
- **Controller Siêu Mỏng (Thin Controllers)**: Controller chỉ nhận request, gọi Service và trả về View / Redirect. Tuyệt đối không viết logic nghiệp vụ phức tạp hoặc câu query DB trực tiếp trong Controller.
- **Service Layer**: Toàn bộ logic xử lý dữ liệu, transaction và query phức tạp được đặt trong `app/Services/`.
- **Form Request Validation**: Không dùng `$request->validate()` trực tiếp trong Controller. Bắt buộc tạo Form Request trong `app/Http/Requests/{Module}/` có message tiếng Việt rõ ràng và thuộc tính tương ứng.
- **Dependency Injection**: Luôn inject Service vào Controller qua Constructor Property Promotion (`public function __construct(protected ModuleService $service) {}`).
- **Strict Typing & Return Types**: Mọi hàm/phương thức phải có đầy đủ kiểu dữ liệu tham số và return type (`View`, `RedirectResponse`, `Collection`, `LengthAwarePaginator`, `bool`, `void`, v.v.).

## 2. Tránh Magic Numbers & Magic Strings
- Không hardcode các con số hoặc chuỗi lặp lại trong code (ví dụ: độ dài chuỗi, limit phân trang, flash session key, thông báo hệ thống).
- Luôn khai báo và sử dụng hằng số trong `App\Constants\AppConstants` hoặc Enums chuyên biệt trong `app/Enums/`.

## 3. Eloquent Model & Database Migrations
- Migration phải định nghĩa rõ kiểu dữ liệu, index, foreign key constraints và `onDelete('cascade')` hoặc `nullOnDelete()`.
- Model phải khai báo `protected $fillable = [...]`, các mối quan hệ (`belongsTo`, `hasMany`, `belongsToMany`), và casting kiểu dữ liệu (`casts`).
