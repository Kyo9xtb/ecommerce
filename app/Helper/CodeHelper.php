<?php

namespace App\Helpers;

class CodeHelper
{
    /**
     * Sinh mã có prefix linh hoạt, ngày và phần ký tự ngẫu nhiên.
     *
     * @param string $prefix Tiền tố (ví dụ: 'TOUR', 'BOOK', 'USER')
     * @param int $length Độ dài phần ký tự ngẫu nhiên
     * @param bool $includeDate Có thêm ngày vào mã hay không
     * @return string
     */
    public static function generate(string $prefix = 'CODE', int $length = 6, bool $includeDate = true): string
    {
        $prefix = strtoupper(trim($prefix));
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // loại bỏ ký tự dễ nhầm
        $date = $includeDate ? date('Ymd') . '-' : '';

        $random = substr(str_shuffle(str_repeat($chars, $length)), 0, $length);

        return "{$prefix}-{$date}{$random}";
    }
}
