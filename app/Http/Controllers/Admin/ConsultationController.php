<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Danh sách lịch hẹn khảo sát đo đạc tận nhà
     */
    public function index(Request $request)
    {
        $query = Consultation::with(['user', 'assignedStaff'])->latest();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo khung giờ
        if ($request->filled('time_slot')) {
            $query->where('preferred_time_slot', $request->time_slot);
        }

        // Lọc theo ngày hẹn
        if ($request->filled('date')) {
            $query->whereDate('preferred_date', $request->date);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('district', 'LIKE', "%{$search}%");
            });
        }

        $consultations = $query->paginate(15)->withQueryString();

        // Thống kê KPI
        $stats = [
            'total' => Consultation::count(),
            'pending' => Consultation::where('status', 'pending')->count(),
            'measuring' => Consultation::whereIn('status', ['assigned', 'measuring'])->count(),
            'completed' => Consultation::whereIn('status', ['quoted', 'completed'])->count(),
        ];

        return view('admin.consultations.index', compact('consultations', 'stats'));
    }

    /**
     * Xem chi tiết lịch hẹn khảo sát
     */
    public function show(Consultation $consultation)
    {
        $consultation->load(['user', 'assignedStaff']);
        $staffMembers = User::whereIn('role', ['admin', 'staff', 'super_admin'])->get();

        return view('admin.consultations.show', compact('consultation', 'staffMembers'));
    }

    /**
     * Cập nhật tiến độ khảo sát, phân công thợ và báo giá
     */
    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,assigned,measuring,quoted,completed,cancelled',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:2000',
            'quoted_amount' => 'nullable|numeric|min:0',
        ], [
            'status.required' => 'Vui lòng chọn trạng thái lịch hẹn.',
            'assigned_staff_id.exists' => 'Nhân viên/Thợ được chọn không tồn tại.',
        ]);

        $consultation->update($validated);

        return back()->with('success', "Cập nhật tiến độ lịch hẹn {$consultation->code} thành công!");
    }

    /**
     * Xóa lịch hẹn
     */
    public function destroy(Consultation $consultation)
    {
        $code = $consultation->code;
        $consultation->delete();

        return redirect()->route('admin.consultations.index')->with('success', "Đã xóa lịch hẹn {$code} thành công.");
    }
}
