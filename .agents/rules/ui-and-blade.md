# Quy Chuẩn Giao Diện (UI/UX) & Blade Template

## 1. Thiết Kế Giao Diện & CSS
- **Không nhúng inline style lớn**: Mọi kiểu dáng giao diện phải sử dụng class tiện ích của Bootstrap 5 hoặc CSS Tokens trong `public/css/custom.css`.
- **CSS Custom Properties**: Màu sắc, bo góc (radius), đổ bóng (shadow) đều lấy từ CSS Variables (`var(--primary-color)`, `var(--radius-md)`, v.v.).

## 2. Cấu Trúc Blade Template & Components
- **Layout dùng chung**: Mọi trang giao diện phải `@extends('layouts.app')` và đặt `@section('title', '...')`, `@section('content')`.
- **Tách Component tái sử dụng**:
  - `<x-navbar />`: Thanh điều hướng chung.
  - `<x-alert />`: Thông báo Flash session (thành công, lỗi, validation).
  - Các modal / dialog xác nhận thao tác nguy hiểm (Xóa, Hủy đơn).
- **Tránh XSS & Lỗi JavaScript**:
  - Khi truyền chuỗi vào hàm JS trên Blade, luôn dùng `addslashes(...)` hoặc `json_encode(...)`.
  - Các thao tác xóa (`DELETE`) bắt buộc dùng Form ẩn kèm `@csrf` và `@method('DELETE')`.
- **Hỗ trợ phân trang chuẩn**:
  - Dùng `$items->links('pagination::bootstrap-5')` hoặc custom pagination.
