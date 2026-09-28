<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /**
     * Hiển thị trang đặt lịch hẹn khảo sát & mang mẫu vải tận nhà
     */
    public function create()
    {
        $categories = Category::all();
        $user = Auth::user();
        $defaultAddress = $user ? $user->addresses()->where('is_default', true)->first() : null;

        return view('consultations.create', compact('categories', 'user', 'defaultAddress'));
    }

    /**
     * Tiếp nhận đăng ký lịch hẹn khảo sát từ khách hàng
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'nullable|string|max:100',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time_slot' => 'required|in:morning,afternoon,evening',
            'curtain_types' => 'nullable|array',
            'estimated_windows' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (10 số, ví dụ 0912345678).',
            'address.required' => 'Vui lòng nhập địa chỉ nhà (số nhà, tên đường, tên tòa nhà).',
            'province.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district.required' => 'Vui lòng nhập Quận/Huyện.',
            'preferred_date.required' => 'Vui lòng chọn ngày hẹn khảo sát mong muốn.',
            'preferred_date.after_or_equal' => 'Ngày hẹn khảo sát phải từ hôm nay trở đi.',
            'preferred_time_slot.required' => 'Vui lòng chọn khung giờ hẹn thuận tiện.',
        ]);

        $code = Consultation::generateCode();
        $validated['code'] = $code;
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $consultation = Consultation::create($validated);

        return redirect()->route('consultations.create')->with([
            'booking_success' => true,
            'consultation_code' => $consultation->code,
            'customer_name' => $consultation->customer_name,
            'preferred_date' => $consultation->preferred_date->format('d/m/Y'),
            'time_slot' => $consultation->time_slot_text,
            'full_address' => $consultation->full_address,
        ]);
    }
}
