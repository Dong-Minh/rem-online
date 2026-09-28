<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Sổ địa chỉ nhận hàng của khách hàng
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest('is_default')->latest()->get();

        return view('profile.addresses.index', compact('addresses'));
    }

    /**
     * Thêm địa chỉ mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'address_detail' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ], [
            'recipient_name.required' => 'Vui lòng nhập họ tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'province.required' => 'Vui lòng nhập Tỉnh/Thành phố.',
            'district.required' => 'Vui lòng nhập Quận/Huyện.',
            'ward.required' => 'Vui lòng nhập Phường/Xã.',
            'address_detail.required' => 'Vui lòng nhập số nhà, tên đường chi tiết.',
        ]);

        $user = Auth::user();
        $isDefault = $request->has('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['is_default'] = $isDefault;

        Address::create($validated);

        return back()->with('success', 'Thêm địa chỉ nhận hàng mới thành công!');
    }

    /**
     * Cập nhật địa chỉ nhận hàng
     */
    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'],
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'address_detail' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->has('is_default');
        if ($isDefault) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $validated['is_default'] = $isDefault;
        $address->update($validated);

        return back()->with('success', 'Cập nhật địa chỉ nhận hàng thành công!');
    }

    /**
     * Đặt làm địa chỉ mặc định
     */
    public function setDefault(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt làm địa chỉ nhận hàng mặc định.');
    }

    /**
     * Xóa địa chỉ
     */
    public function destroy(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $firstAddress = Auth::user()->addresses()->first();
            if ($firstAddress) {
                $firstAddress->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Đã xóa địa chỉ thành công.');
    }
}
