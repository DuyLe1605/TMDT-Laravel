<?php

namespace App\Constants;

final class AppConstants
{
    // Phân trang
    public const DEFAULT_PAGINATION_LIMIT = 10;
    public const ADMIN_PAGINATION_LIMIT = 15;

    // Giới hạn độ dài chuỗi
    public const MAX_STRING_LENGTH = 255;
    public const MAX_TEXT_LENGTH = 2000;

    // Flash session keys
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';
    public const FLASH_WARNING = 'warning';
    public const FLASH_INFO = 'info';

    // Thông báo mặc định cho Category
    public const MSG_CATEGORY_CREATED = 'Danh mục túi xách đã được tạo mới thành công.';
    public const MSG_CATEGORY_UPDATED = 'Danh mục túi xách đã được cập nhật thành công.';
    public const MSG_CATEGORY_DELETED = 'Danh mục túi xách đã được xóa thành công.';
    public const MSG_CATEGORY_NOT_FOUND = 'Không tìm thấy danh mục yêu cầu.';

    // Thông báo mặc định cho Product
    public const MSG_PRODUCT_CREATED = 'Sản phẩm túi xách nữ đã được thêm mới thành công.';
    public const MSG_PRODUCT_UPDATED = 'Thông tin sản phẩm túi xách đã được cập nhật thành công.';
    public const MSG_PRODUCT_DELETED = 'Sản phẩm túi xách đã được xóa thành công.';
    public const MSG_PRODUCT_NOT_FOUND = 'Không tìm thấy sản phẩm yêu cầu.';
}
