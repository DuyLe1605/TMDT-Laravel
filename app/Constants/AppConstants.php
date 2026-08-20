<?php

namespace App\Constants;

final class AppConstants
{
    // Phân trang
    public const DEFAULT_PAGINATION_LIMIT = 10;
    public const ADMIN_PAGINATION_LIMIT = 20;

    // Giới hạn độ dài chuỗi
    public const MAX_STRING_LENGTH = 255;
    public const MAX_TEXT_LENGTH = 1000;

    // Flash session keys
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';
    public const FLASH_WARNING = 'warning';
    public const FLASH_INFO = 'info';

    // Thông báo mặc định cho Category
    public const MSG_CATEGORY_CREATED = 'Danh mục đã được tạo mới thành công.';
    public const MSG_CATEGORY_UPDATED = 'Danh mục đã được cập nhật thành công.';
    public const MSG_CATEGORY_DELETED = 'Danh mục đã được xóa thành công.';
    public const MSG_CATEGORY_NOT_FOUND = 'Không tìm thấy danh mục yêu cầu.';
}
