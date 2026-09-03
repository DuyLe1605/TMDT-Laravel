<?php

namespace App\Helpers;

class VietnameseHelper
{
    /**
     * Chuyển đổi chuỗi Tiếng Việt có dấu thành không dấu, chuyển thành chữ thường.
     * Ví dụ: "Túi Xách Nữ Da Thật" -> "tui xach nu da that"
     *
     * @param string|null $str
     * @return string
     */
    public static function removeAccents(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        // Chuyển ký tự unicode chuẩn NFC
        $str = normalizer_is_normalized($str) ? $str : normalizer_normalize($str);

        // Bảng ánh xạ ký tự tiếng Việt có dấu sang không dấu
        $accents = [
            'a' => ['á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ'],
            'd' => ['đ', 'Đ'],
            'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ'],
            'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị'],
            'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ'],
            'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự'],
            'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'],
        ];

        foreach ($accents as $replacement => $chars) {
            $str = str_replace($chars, $replacement, $str);
        }

        // Chuyển các ký tự còn lại (nếu có dấu tổ hợp)
        $str = preg_replace('/[\x{0300}-\x{036f}]/u', '', $str);

        // Chuẩn hóa khoảng trắng và chuyển chữ thường
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Tạo chuỗi search_index tổng hợp từ các trường thông tin sản phẩm.
     *
     * @param string|null ...$fields
     * @return string
     */
    public static function buildSearchIndex(?string ...$fields): string
    {
        $collected = [];
        foreach ($fields as $field) {
            if (!empty($field)) {
                $collected[] = self::removeAccents($field);
            }
        }

        return implode(' ', array_unique(array_filter($collected)));
    }
}
