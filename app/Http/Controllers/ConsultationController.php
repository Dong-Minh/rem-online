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
        // Chuẩn hóa số điện thoại (bỏ dấu cách, dấu chấm, chuyển +84 thành 0)
        if ($request->has('phone')) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', (string)$request->input('phone'));
            if (str_starts_with($cleanedPhone, '84') && strlen($cleanedPhone) === 11) {
                $cleanedPhone = '0' . substr($cleanedPhone, 2);
            }
            $request->merge(['phone' => $cleanedPhone]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'nullable|string|max:100',
            'preferred_date' => 'required|date',
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
            'preferred_time_slot.required' => 'Vui lòng chọn khung giờ hẹn thuận tiện.',
        ]);

        try {
            $code = Consultation::generateCode();
            $validated['code'] = $code;
            $validated['user_id'] = Auth::id();
            $validated['status'] = 'pending';

            // Đảm bảo tương thích 100% với các trường cũ nếu database chưa migrate kịp
            $validated['name'] = $validated['customer_name'];
            $validated['content'] = !empty($validated['notes']) ? $validated['notes'] : 'Yêu cầu khảo sát và đo đạc rèm tận nhà.';
            if (!empty($validated['curtain_types']) && is_array($validated['curtain_types'])) {
                $validated['curtain_type_interest'] = implode(', ', $validated['curtain_types']);
            }

            $consultation = Consultation::create($validated);

            return redirect()->route('consultations.create')->with([
                'booking_success' => true,
                'consultation_code' => $consultation->code,
                'customer_name' => $consultation->customer_name,
                'preferred_date' => $consultation->preferred_date ? $consultation->preferred_date->format('d/m/Y') : date('d/m/Y'),
                'time_slot' => $consultation->time_slot_text,
                'full_address' => $consultation->full_address,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Consultation store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withInput()->withErrors([
                'general' => 'Có lỗi xảy ra khi gửi yêu cầu khảo sát: ' . $e->getMessage() . '. Vui lòng liên hệ hotline 0901.234.567 để được hỗ trợ nhanh nhất.',
            ]);
        }
    }
}
