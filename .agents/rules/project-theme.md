# Quy Chuẩn Đề Tài: Sàn Thương Mại Điện Tử Chuyên Biệt - TÚI XÁCH NỮ

## 1. Định Vị Ngành Hàng (Domain & Business Scope)
- **Chủ đề dự án**: Hệ thống Thương Mại Điện Tử chuyên kinh doanh **Túi Xách Nữ Thời Trang & Cao Cấp (Women's Handbags & Fashion Bags)**.
- **Phong cách thiết kế**: Thanh lịch, hiện đại, sang trọng (Modern Luxury & Clean SaaS).
- **Phân khúc sản phẩm**: Túi thời trang công sở, túi dạo phố, túi dạ tiệc, balo mini, ví & clutch cao cấp.

## 2. Chuẩn Ngành Hàng & Phân Loại Danh Mục (Categories)
Mọi dữ liệu mẫu và danh mục cốt lõi phải xoay quanh các phân nhóm:
1. **Túi Đeo Chéo (Crossbody Bags)**: Tiện dụng, trẻ trung, dạo phố.
2. **Túi Xách Tay Công Sở (Handbags / Satchels)**: Thanh lịch, sang trọng, đựng vừa tài liệu/ipad.
3. **Túi Tote Đa Năng (Tote Bags)**: Sức chứa lớn, đi học, đi làm, phong cách tối giản.
4. **Túi Kẹp Nách Thời Thượng (Shoulder / Baguette Bags)**: Xu hướng Y2K, quyến rũ, thời thượng.
5. **Ví Cầm Tay & Clutch Dạ Tiệc (Clutch & Evening Bags)**: Đính đá, da bóng, dạ tiệc sang trọng.
6. **Balo Mini Nữ Thời Trang (Mini Backpacks)**: Năng động, cá tính.

## 3. Thuộc Tính Sản Phẩm Đặc Thù (Product Attributes)
Khi thiết kế bảng `products` và form nhập liệu:
- **Tên sản phẩm**: Đặt theo chuẩn thời trang (ví dụ: *Túi Đeo Chéo Nữ Da Bò Cao Cấp Aurelia*, *Túi Xách Tay Công Sở Vân Cá Sấu Monogram*, v.v.).
- **Chất liệu (Material)**: Da bò tự nhiên, Da PU cao cấp, Vải Canvas chống thấm, Da tổng hợp cao cấp, v.v.
- **Kích thước (Dimensions)**: Dài x Rộng x Cao (cm) (ví dụ: `22 x 8 x 15 cm`).
- **Khoảng giá (Price)**: Tính theo đơn vị VNĐ (ví dụ: `450.000đ - 2.850.000đ`).
- **Màu sắc & Trạng thái tồn kho**: Đỏ Bordeaux, Trắng Kem, Đen Obsidian, Hồng Pastel, Nâu Caramel.

## 4. Trải Nghiệm Tìm Kiếm & Lọc (Search & Filter Conventions)
- Hỗ trợ tìm kiếm **Tiếng Việt Không Dấu** (tìm `tui deo cheo` match `Túi đeo chéo`).
- Hỗ trợ sắp xếp theo **Ngày tạo mới nhất/cũ nhất**, **Giá bán tăng/giảm dần**, **Tồn kho**, **Tên A-Z**.
