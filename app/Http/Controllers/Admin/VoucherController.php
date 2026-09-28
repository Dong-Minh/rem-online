<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    /**
     * Danh sách tất cả mã giảm giá
     */
    public function index(Request $request)
    {
        $query = Voucher::withCount('usages')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->where('is_active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now()),
                'expired' => $query->where('ends_at', '<', now()),
                'upcoming' => $query->where('starts_at', '>', now()),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        $vouchers = $query->paginate(10)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Form tạo mã giảm giá mới
     */
    public function create()
    {
        return view('admin.vouchers.create');
    }

    /**
     * Lưu mã giảm giá mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9_\-]+$/i', 'unique:vouchers,code'],
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trên hệ thống.',
            'code.regex' => 'Mã giảm giá chỉ được gồm chữ cái, chữ số và dấu gạch nối (Ví dụ: REM50K).',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá (% hoặc tiền cố định).',
            'discount_value.required' => 'Vui lòng nhập mức giảm giá.',
            'discount_value.min' => 'Mức giảm giá phải lớn hơn 0.',
            'ends_at.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo mới mã giảm giá ' . $validated['code'] . ' thành công!');
    }

    /**
     * Xem chi tiết & lịch sử sử dụng của Voucher
     */
    public function show(Voucher $voucher)
    {
        $voucher->load(['usages.user', 'usages.order']);
        $usages = $voucher->usages()->latest('used_at')->paginate(15);

        return view('admin.vouchers.show', compact('voucher', 'usages'));
    }

    /**
     * Form chỉnh sửa mã giảm giá
     */
    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Cập nhật mã giảm giá
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9_\-]+$/i', Rule::unique('vouchers', 'code')->ignore($voucher->id)],
            'description' => 'nullable|string|max:500',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'boolean',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trên hệ thống.',
            'code.regex' => 'Mã giảm giá chỉ được gồm chữ cái, chữ số và dấu gạch nối.',
            'discount_value.required' => 'Vui lòng nhập mức giảm giá.',
            'ends_at.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->has('is_active');
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật mã giảm giá ' . $voucher->code . ' thành công!');
    }

    /**
     * Xóa mã giảm giá
     */
    public function destroy(Voucher $voucher)
    {
        $code = $voucher->code;
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xóa mã giảm giá ' . $code . ' thành công.');
    }
}
