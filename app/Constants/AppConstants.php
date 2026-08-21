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

    // Thông báo cho Auth
    public const MSG_LOGIN_SUCCESS = 'Đăng nhập thành công! Chào mừng bạn quay lại.';
    public const MSG_LOGIN_FAILED = 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại.';
    public const MSG_REGISTER_SUCCESS = 'Đăng ký tài khoản thành công! Hãy đăng nhập để tiếp tục.';
    public const MSG_REGISTER_FAILED = 'Đăng ký thất bại. Vui lòng thử lại sau.';
    public const MSG_LOGOUT_SUCCESS = 'Bạn đã đăng xuất thành công.';

    // Thông báo cho User Management
    public const MSG_USER_CREATED = 'Tài khoản người dùng đã được tạo mới thành công.';
    public const MSG_USER_UPDATED = 'Thông tin tài khoản đã được cập nhật thành công.';
    public const MSG_USER_DELETED = 'Tài khoản người dùng đã được xóa thành công.';
    public const MSG_USER_NOT_FOUND = 'Không tìm thấy tài khoản người dùng yêu cầu.';
    public const MSG_USER_CANNOT_DELETE_SELF = 'Bạn không thể tự xóa tài khoản quản trị đang đăng nhập của chính mình.';
    public const MSG_USER_CANNOT_DELETE_LAST_ADMIN = 'Không thể xóa Quản trị viên duy nhất còn lại trong hệ thống.';
}
