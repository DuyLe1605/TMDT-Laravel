# TMDT E-Commerce - Laravel Web Application

Hệ thống Website Thương mại Điện tử được xây dựng trên nền tảng **PHP 8.3 & Laravel 11/12** với kiến trúc đa tầng chuẩn Enterprise (Thin Controller, Service Layer, Form Request Validation, Blade Components).

---

## ⚡ Khởi Chạy Nhanh (1-Click Start)
Nhấp đúp chuột vào file **`start.bat`** (hoặc chạy `./start.ps1` trong PowerShell).

Dự án sẽ tự động khởi động tại: **`http://127.0.0.1:8000`**

---

## 📖 Tài Liệu Chi Tiết
Xem toàn bộ báo cáo kiến trúc, sơ đồ ERD cơ sở dữ liệu và hướng dẫn làm các bài lab tại file:  
👉 **[DOCUMENTATION.md](DOCUMENTATION.md)**

---

## 📁 Cấu Trúc Thư Mục Chuẩn
* `app/Constants/AppConstants.php`: Quản lý hằng số hệ thống, triệt tiêu Magic Numbers & Strings.
* `app/Http/Requests/Category/`: Form Requests đóng gói validation và thông báo tiếng Việt.
* `app/Services/CategoryService.php`: Service Layer xử lý logic nghiệp vụ và truy vấn CSDL.
* `app/Http/Controllers/CategoryController.php`: Thin Controller điều phối request.
* `resources/views/components/`: Reusable Blade UI Components (`<x-navbar />`, `<x-alert />`).
* `public/css/custom.css`: Hệ thống biến màu và CSS Tokens chuẩn quốc tế.
* `.agents/`: Quy chuẩn thiết kế (Rules) và Kỹ năng tự động hóa (Skills) hỗ trợ phát triển các bài lab tiếp theo.
