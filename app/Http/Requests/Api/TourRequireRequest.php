<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class TourRequireRequest extends Request
{
    public function rules(): array
    {
        $method = request()->method();

        if ($method === 'DELETE') {
            return [
                'id' => ['required', 'int'],
            ];
        }

        if (!in_array($method, ['POST', 'PUT'], true)) {
            return [];
        }

        $rules = [
            'full_name' => ['required', 'string'],
            'nationality' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'feedback' => ['required', 'int', 'in:1,2'],
            'expected_destination' => ['required', 'string'],
            'departure_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'expected_month' => ['nullable', 'string'],
            'expected_year' => ['nullable', 'string'],
            'number_days' => ['nullable', 'int'],
            'vehicle' => ['nullable', 'numeric'],
            'adult' => ['nullable', 'int', 'min:1'],
            'children' => ['nullable', 'int', 'min:0'],
            'baby' => ['nullable', 'int', 'min:0'],
            'number_rooms' => ['nullable', 'int'],
            'hotel_standards' => ['nullable', 'int'],
            'note' => ['nullable', 'string'],
            'status' => ['nullable', 'int', 'in:0,1'], // 0: Inactive, 1: Active
        ];

        if ($method === 'PUT') {
            $rules['id'] = ['required', 'int'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'nationality.required' => 'Vui lòng chọn quốc tịch.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'feedback.required' => 'Vui lòng chọn phản hồi.',
            'feedback.int' => 'Phản hồi phải là số nguyên.',
            'feedback.in' => 'Phản hồi không hợp lệ (1: Tốt, 2: Chưa tốt).',
            'expected_destination.required' => 'Vui lòng nhập điểm đến mong muốn.',
            'departure_date.date' => 'Ngày khởi hành không đúng định dạng.',
            'end_date.date' => 'Ngày kết thúc không đúng định dạng.',
            'expected_month.string' => 'Tháng dự kiến phải là chuỗi ký tự.',
            'expected_year.string' => 'Năm dự kiến phải là chuỗi ký tự.',
            'number_days.int' => 'Số ngày phải là số nguyên.',
            'vehicle.numeric' => 'Phương tiện phải là kiểu số.',
            'adult.int' => 'Số người lớn phải là số nguyên.',
            'adult.min' => 'Phải có ít nhất một người lớn.',
            'children.int' => 'Số trẻ em phải là số nguyên.',
            'children.min' => 'Số trẻ em không được âm.',
            'baby.int' => 'Số em bé phải là số nguyên.',
            'baby.min' => 'Số em bé không được âm.',
            'number_rooms.int' => 'Số phòng phải là số nguyên.',
            'hotel_standards.int' => 'Tiêu chuẩn khách sạn phải là số nguyên.',
            'note.string' => 'Ghi chú phải là chuỗi ký tự.',
            'status.int' => 'Trạng thái phải là số nguyên.',
            'status.in' => 'Trạng thái không hợp lệ (0: Không hoạt động, 1: Hoạt động).',
        ];
    }
}
