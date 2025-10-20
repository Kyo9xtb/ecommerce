<?php

namespace App\Enum\Authen;

enum AuthenStatusCode: int
{
    const NONE_ERR = 0;
    // ✅ Thành công chung
    const SUCCESS = 200;

    // ⚠️ Lỗi xác thực & quyền hạn
    const UNAUTHORIZED = 401;          // Token không hợp lệ hoặc chưa đăng nhập
    const FORBIDDEN = 403;             // Đã đăng nhập nhưng không đủ quyền
    const NOT_FOUND = 404;             // Không tìm thấy tài nguyên
    const METHOD_NOT_ALLOWED = 405;    // Phương thức không được phép (VD: POST thay vì GET)
    const CONFLICT = 409;              // Xung đột dữ liệu (VD: email đã tồn tại)
    const TOO_MANY_REQUESTS = 429;     // Gửi request quá nhiều (rate limit)

    // ⚠️ Lỗi client (yêu cầu không hợp lệ)
    const BAD_REQUEST = 400;           // Dữ liệu request không hợp lệ
    const PARAMS_INVALID = 422;        // Validate sai dữ liệu đầu vào (Laravel validation)
    const UNSUPPORTED_MEDIA_TYPE = 415; // Sai định dạng Content-Type (VD: không phải JSON)

    // ⚙️ Lỗi hệ thống
    const SERVER_ERR = 500;            // Lỗi máy chủ
    const SERVICE_UNAVAILABLE = 503;   // Dịch vụ tạm ngừng
    const GATEWAY_TIMEOUT = 504;       // Quá thời gian phản hồi

    // 🔐 Tuỳ chọn khác cho xác thực
    const TOKEN_EXPIRED = 498;         // Token hết hạn (thường dùng custom)
    const INVALID_SIGNATURE = 499;     // Token sai chữ ký (custom)
}
