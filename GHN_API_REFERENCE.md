# TÀI LIỆU TOÀN DIỆN VỀ HỆ THỐNG API GIAO HÀNG NHANH (GHN EXPRESS API V3)
> **Tài liệu tham khảo kỹ thuật phục vụ tích hợp cho dự án TMDT Laravel (Aurelia E-Commerce)**  
> Cập nhật theo tài liệu chính thức tại: [https://api.ghn.vn/home/docs/detail](https://api.ghn.vn/home/docs/detail)

---

## 📌 1. TỔNG QUAN MÔI TRƯỜNG & XÁC THỰC (AUTHENTICATION)

### 1.1. Các môi trường kết nối (Environments)

| Tham số | Môi trường Test (Staging / Dev) | Môi trường Thật (Production) |
| :--- | :--- | :--- |
| **Cổng Web Quản lý** | [https://5sao.ghn.dev](https://5sao.ghn.dev) | [https://khachhang.ghn.vn](https://khachhang.ghn.vn) |
| **Base API URL** | `https://dev-online-gateway.ghn.vn/shiip/public-api` | `https://online-gateway.ghn.vn/shiip/public-api` |
| **Mục đích sử dụng** | Phát triển (Dev), kiểm thử (Test), demo đồ án | Kinh doanh thực tế, giao nhận bưu phẩm thật |

### 1.2. Hướng dẫn lấy Token và Shop ID (Môi trường Dev)
1. Truy cập [https://5sao.ghn.dev](https://5sao.ghn.dev) và đăng ký tài khoản thử nghiệm (bằng số điện thoại bất kỳ).
2. Đăng nhập -> Chọn mục **Chủ cửa hàng** ở góc trên bên phải.
3. Nhấp vào nút **Xem** tại dòng Token và sao chép mã `API Token` (chuỗi UUID dạng: `637170d5-942b-11ea-9821-0281a26fb5d4`).
4. Vào menu **Quản lý cửa hàng** -> Xem mã `Shop ID` (dạng số nguyên, ví dụ: `885` hoặc `12345`).
5. Cập nhật thông tin địa chỉ kho lấy hàng của Shop (Tỉnh, Huyện, Xã) để GHN có toạ độ điểm gửi hàng mặc định.

### 1.3. Chuẩn HTTP Headers bắt buộc cho mọi Request
Tất cả các lệnh gọi API gửi đến GHN đều phải đính kèm Header sau:
```http
Content-Type: application/json
Token: <GHN_API_TOKEN>
ShopId: <GHN_SHOP_ID>
```
*(Ghi chú: Header `ShopId` là bắt buộc với các API liên quan đến đơn hàng, tính cước phí theo bưu cục, xem cửa hàng).*

---

## 🗺️ 2. DANH MỤC CÁC NHÓM API HIỆN CÓ CỦA GHN

```text
GHN API v3 Architecture
├── 1. Master Data (Địa lý hành chính: Tỉnh / Huyện / Xã)
├── 2. Calculate Fee & Leadtime (Tính cước phí & Thời gian giao hàng)
├── 3. Shipping Orders (Tạo đơn, Hủy đơn, In vận đơn, Tra cứu hành trình)
├── 4. Store & Station (Quản lý kho lấy hàng & Bưu cục gần nhất)
├── 5. Webhook Callbacks (Bắn thông báo trạng thái đơn hàng theo thời gian thực)
└── 6. Ticket Management (Tạo yêu cầu khiếu nại, hỗ trợ đền bù)
```

---

## 📂 3. CHI TIẾT TỪNG NHÓM API

### NHÓM 1: ĐỊA LÝ HÀNH CHÍNH (MASTER DATA)
*Dùng để nạp dữ liệu Tỉnh/Huyện/Xã chuẩn mã số GHN vào dropdown chọn địa chỉ trên trang Checkout.*

#### 1.1. Lấy danh sách Tỉnh / Thành phố (Get Province)
* **Method:** `GET`
* **Endpoint:** `/master-data/province`
* **Headers:** `Token: <token>`, `Content-Type: application/json`
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "ProvinceID": 201,
      "ProvinceName": "Hà Nội",
      "Code": "4"
    },
    {
      "ProvinceID": 202,
      "ProvinceName": "Hồ Chí Minh",
      "Code": "8"
    },
    {
      "ProvinceID": 203,
      "ProvinceName": "Đà Nẵng",
      "Code": "511"
    }
  ]
}
```

#### 1.2. Lấy danh sách Quận / Huyện theo Tỉnh (Get District)
* **Method:** `GET` hoặc `POST`
* **Endpoint:** `/master-data/district`
* **Query Params / Body:** `{"province_id": 202}`
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "DistrictID": 1442,
      "ProvinceID": 202,
      "DistrictName": "Quận 1",
      "Code": "1442",
      "Type": 2
    },
    {
      "DistrictID": 1444,
      "ProvinceID": 202,
      "DistrictName": "Quận 3",
      "Code": "1444",
      "Type": 2
    }
  ]
}
```

#### 1.3. Lấy danh sách Phường / Xã theo Huyện (Get Ward)
* **Method:** `GET` hoặc `POST`
* **Endpoint:** `/master-data/ward`
* **Query Params / Body:** `{"district_id": 1442}`
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "WardCode": "20101",
      "DistrictID": 1442,
      "WardName": "Phường Bến Nghé"
    },
    {
      "WardCode": "20102",
      "DistrictID": 1442,
      "WardName": "Phường Bến Thành"
    }
  ]
}
```

---

### NHÓM 2: TÍNH CƯỚC PHÍ & DỰ ĐOÁN THỜI GIAN GIAO HÀNG (CALCULATE FEE & LEADTIME)
*(Đây là nhóm cốt lõi phục vụ tính năng + tiền ship dựa trên địa chỉ nhận hàng)*

#### 2.1. Lấy danh sách gói dịch vụ chuyển phát khả dụng (Get Available Services)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/available-services`
* **Mục đích:** Kiểm tra xem tuyến đường giữa kho hàng và khách hàng hỗ trợ những gói dịch vụ nào (Chuẩn, Tiết kiệm, Hỏa tốc).
* **Payload Request:**
```json
{
  "shop_id": 885,
  "from_district": 1442,
  "to_district": 1454
}
```
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "service_id": 53320,
      "short_name": "Giao tiêu chuẩn",
      "service_type_id": 2
    },
    {
      "service_id": 53321,
      "short_name": "Giao hỏa tốc",
      "service_type_id": 1
    }
  ]
}
```

#### 2.2. Tính cước phí vận chuyển chính xác (Calculate Shipping Fee)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/fee`
* **Payload Request:**
```json
{
  "from_district_id": 1442,
  "from_ward_code": "20101",
  "to_district_id": 1452,
  "to_ward_code": "21012",
  "service_type_id": 2,
  "service_id": null,
  "height": 15,
  "length": 25,
  "width": 20,
  "weight": 800,
  "insurance_value": 500000,
  "cod_failed_amount": 2000,
  "coupon": null,
  "items": [
    {
      "name": "Túi Xách Da Cao Cấp Aurelia Classic",
      "quantity": 1,
      "height": 15,
      "weight": 800,
      "length": 25,
      "width": 20
    }
  ]
}
```
* **Ý nghĩa các trường quan trọng:**
  * `from_district_id` & `from_ward_code`: Vị trí kho của shop (nếu để trống, GHN lấy theo cấu hình ShopId).
  * `to_district_id` & `to_ward_code`: Vị trí người nhận hàng.
  * `service_type_id`: `1` (Hỏa tốc) hoặc `2` (Giao hàng tiêu chuẩn E-Commerce).
  * `weight`: Trọng lượng tính bằng **Gram** (ví dụ 800g).
  * `length, width, height`: Kích thước đóng gói tính bằng **Centimet (cm)** để tính cân nặng quy đổi thể tích `(Dài x Rộng x Cao) / 5000`.
  * `insurance_value`: Giá trị khai giá bảo hiểm hàng hóa (tối đa 5.000.000 ₫).
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "total": 32500,
    "service_fee": 30000,
    "insurance_fee": 2500,
    "pick_station_fee": 0,
    "coupon_value": 0,
    "r2s_fee": 0,
    "document_return": 0,
    "double_check": 0,
    "cod_fee": 0,
    "pick_remote_areas_fee": 0,
    "deliver_remote_areas_fee": 0,
    "cod_failed_fee": 0
  }
}
```
> **Trường cần lấy:** `data.total` chính là số tiền phí vận chuyển cuối cùng mà khách phải trả.

#### 2.3. Dự kiến ngày giờ phát hàng (Calculate Delivery Leadtime)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/leadtime`
* **Payload Request:**
```json
{
  "from_district_id": 1442,
  "from_ward_code": "20101",
  "to_district_id": 1452,
  "to_ward_code": "21012",
  "service_id": 53320
}
```
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "leadtime": 1725595199,
    "order_date": 1725422400
  }
}
```
> `leadtime` trả về timestamp Unix. Format sang ngày giao dự kiến (ví dụ: *Giao dự kiến vào Thứ Sáu, 06/09/2026*).

---

### NHÓM 3: QUẢN LÝ ĐƠN HÀNG (SHIPPING ORDERS)

#### 3.1. Tạo đơn giao hàng sang GHN (Create Shipping Order)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/create`
* **Mục đích:** Đẩy đơn hàng từ website sang hệ thống GHN để bưu tá đến lấy hàng đi giao.
* **Payload Request:**
```json
{
  "payment_type_id": 2,
  "note": "Cho khách xem hàng trước khi nhận, không cho thử",
  "required_note": "CHOTHUHANG",
  "client_order_code": "AUR-20260904-ABC12",
  "to_name": "Nguyễn Văn A",
  "to_phone": "0987654321",
  "to_address": "Số 123 Đường Lê Lợi, Phường Bến Thành",
  "to_ward_code": "20102",
  "to_district_id": 1442,
  "cod_amount": 1450000,
  "content": "Đơn hàng thời trang túi xách cao cấp",
  "weight": 800,
  "length": 25,
  "width": 20,
  "height": 15,
  "service_type_id": 2,
  "insurance_value": 1450000,
  "items": [
    {
      "name": "Aurelia Monogram Tote Bag - Màu Nâu",
      "code": "AUR-BAG-01",
      "quantity": 1,
      "price": 1450000,
      "weight": 800
    }
  ]
}
```
* **Ghi chú tham số:**
  * `payment_type_id`: 
    * `1`: Shop trả cước vận chuyển (người gửi trả).
    * `2`: Khách nhận trả cước vận chuyển (người nhận trả).
  * `required_note`:
    * `CHOTHUHANG`: Cho thử hàng.
    * `CHOXEMHANGKHONGTHU`: Cho xem hàng nhưng không cho thử.
    * `KHONGCHOXEMHANG`: Không cho xem hàng.
  * `cod_amount`: Tiền thu hộ COD (nếu khách đã thanh toán MoMo/Bank thì điền `0`).
  * `client_order_code`: Mã đơn trên website Laravel của bạn (dễ đối soát).
* **Response Mẫu (200 OK):**
```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "order_code": "GHN7AZ9KQW",
    "sort_code": "HCM-1442-20102",
    "trans_type": "truck",
    "ward_encode": "",
    "district_encode": "",
    "fee": {
      "main_service": 30000,
      "insurance": 2500,
      "total": 32500
    },
    "total_fee": 32500,
    "expected_delivery_time": "2026-09-06T17:00:00Z"
  }
}
```
> Lưu lại `order_code` (mã vận đơn GHN) vào bảng `orders` của website để theo dõi.

#### 3.2. Tra cứu chi tiết đơn hàng (Order Detail)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/detail`
* **Payload Request:** `{"order_code": "GHN7AZ9KQW"}`

Hoặc tra cứu bằng mã nội bộ của website:
* **Endpoint:** `/v2/shipping-order/detail-by-client-code`
* **Payload Request:** `{"client_order_code": "AUR-20260904-ABC12"}`

#### 3.3. Hủy đơn giao hàng (Cancel Order)
* **Method:** `POST`
* **Endpoint:** `/v2/shipping-order/cancel`
* **Payload Request:**
```json
{
  "order_codes": ["GHN7AZ9KQW"]
}
```

#### 3.4. In tem bưu phẩm / phiếu giao hàng (Print A5 / 80x80)
* **Method:** `GET`
* **Endpoint:** `/v2/a5/gen-token`
* **Query Params:** `?order_codes=GHN7AZ9KQW`
* **Response:** Trả về `token_print`. Sau đó bạn chỉ cần mở URL:
  * In khổ A5: `https://dev-online-gateway.ghn.vn/a5/index-80-80?token_print={token}`
  * In nhiệt 52x70 hoặc 80x80: `https://dev-online-gateway.ghn.vn/a5/index-52-70?token_print={token}`

#### 3.5. Xem ca lấy hàng của shipper (Pick Shift)
* **Method:** `GET`
* **Endpoint:** `/v2/shift/date`
* **Mục đích:** Biết shipper sẽ đến lấy hàng vào buổi sáng hay buổi chiều.

---

### NHÓM 4: CỬA HÀNG & BƯU CỤC (STORE & STATION)
* `GET /v2/shop/all`: Lấy danh sách tất cả các chi nhánh kho đã tạo của Shop.
* `POST /v2/shop/register`: Đăng ký kho hàng mới (truyền `district_id`, `ward_code`, `name`, `phone`, `address`).
* `GET /v2/station/get`: Lấy danh sách bưu cục GHN gần vị trí người mua để khách có thể đến tự nhận hàng (Pick Station).

---

### NHÓM 5: WEBHOOK TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI (CALLBACKS)

Khi shipper giao hàng hoặc cập nhật trạng thái, GHN sẽ bắn 1 HTTP `POST` Request về URL do bạn cấu hình (Webhook Callback URL).

* **Payload GHN gửi về Webhook:**
```json
{
  "OrderCode": "GHN7AZ9KQW",
  "ClientOrderCode": "AUR-20260904-ABC12",
  "Status": "delivered",
  "StatusName": "Đã giao hàng thành công",
  "Time": "2026-09-06T15:30:00Z",
  "Type": "switch_status",
  "Warehouse": "Bưu cục Bến Nghé Q1",
  "ShipperName": "Trần Văn Shipper",
  "ShipperPhone": "0912345678",
  "CODAmount": 1450000
}
```

#### Bảng ánh xạ mã trạng thái GHN (Shipping Status):
| Mã trạng thái GHN | Diễn giải | Ánh xạ sang cột `orders.shipping_status` |
| :--- | :--- | :--- |
| `ready_to_pick` | Đơn hàng mới tạo, đang chờ shipper đến lấy | `pending` |
| `picking` | Shipper đang đi lấy hàng tại kho | `processing` |
| `storing` | Hàng đã nhập về bưu cục trung chuyển | `processing` |
| `delivering` | Shipper đang đi giao hàng cho khách | `shipping` |
| `delivered` | Giao hàng thành công | `delivered` (kèm đổi `payment_status = paid` nếu COD) |
| `delivery_fail` | Giao hàng không thành công (khách không nghe máy, hẹn lại) | `shipping` (thông báo chú ý) |
| `waiting_to_return` | Đang chờ xác nhận chuyển hoàn | `cancelled` |
| `returned` | Hàng đã trả về lại kho shop | `cancelled` |
| `cancel` | Đơn hàng đã bị hủy | `cancelled` |

---

## 🏗️ 4. KIẾN TRÚC ĐỀ XUẤT KHI TÍCH HỢP VÀO DỰ ÁN LARAVEL

Khi bạn sẵn sàng triển khai mã nguồn, cấu trúc sẽ được tích hợp chuẩn theo các tầng logic của dự án như sau:

### 4.1. File cấu hình `.env` và `config/services.php`
```env
# Cấu hình GHN Express Sandbox
GHN_API_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
GHN_API_TOKEN=637170d5-942b-11ea-9821-0281a26fb5d4
GHN_SHOP_ID=885

# Địa chỉ kho xuất hàng mặc định của Shop (Ví dụ: Q1, TP.HCM)
SHOP_ORIGIN_DISTRICT_ID=1442
SHOP_ORIGIN_WARD_CODE=20101
```

### 4.2. Tầng dịch vụ `app/Services/Shipping/GhnShippingService.php`
* **`getProvinces()`**: Lấy danh sách tỉnh thành (lưu `Cache::remember` 30 ngày).
* **`getDistricts($provinceId)`**: Lấy danh sách quận huyện theo tỉnh (cache 30 ngày).
* **`getWards($districtId)`**: Lấy danh sách phường xã theo huyện (cache 30 ngày).
* **`calculateShippingFee($toDistrictId, $toWardCode, $totalWeight, $insuranceAmount)`**: Tính tiền ship chuẩn xác từ GHN.
* **Cơ chế Fallback (Bảo vệ)**: Nếu mạng chập chờn hoặc GHN timeout > 3 giây, tự động trả về giá tiêu chuẩn `30.000 ₫` để khách không bị gián đoạn trải nghiệm mua hàng.

### 4.3. Endpoint AJAX nội bộ cho Frontend
* `POST /api/shipping/calculate-fee`
  * Frontend gửi `district_id`, `ward_code`, danh sách `items[]`.
  * Backend tính tổng khối lượng, gọi GHN và trả về:
    ```json
    {
      "success": true,
      "fee": 32500,
      "formatted_fee": "32.500 ₫",
      "leadtime": "Giao dự kiến vào Thứ Sáu, 06/09"
    }
    ```

---
*Tài liệu được biên soạn phục vụ phát triển đồ án E-Commerce Aurelia TMDT.*
