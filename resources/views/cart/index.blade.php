@extends('layouts.client')

@section('title', 'Giỏ Hàng Của Bạn — Rèm Online')

@section('content')
<!-- Breadcrumb Header -->
<div class="py-3 bg-light border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-bag-check text-warning me-2"></i>Giỏ Hàng Rèm Của Bạn</h3>
            <p class="text-muted small mb-0">Tích chọn các bộ rèm bạn muốn thanh toán ngay, hoặc để lại trong giỏ cho lần mua sau.</p>
        </div>
        @if ($cartItems->isNotEmpty())
            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Xóa toàn bộ giỏ hàng
                </button>
            </form>
        @endif
    </div>

    @if ($cartItems->isEmpty())
        <!-- Trạng thái giỏ hàng trống -->
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
            <div class="bg-light rounded-circle mx-auto p-4 d-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                <i class="bi bi-cart-x fs-1 text-muted"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted small mb-4">Bạn chưa chọn mẫu rèm nào. Hãy khám phá bộ sưu tập rèm may đo cao cấp của chúng tôi ngay nhé!</p>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-gold px-4 py-2">
                    <i class="bi bi-shop me-2"></i> Khám Phá Bộ Sưu Tập Rèm
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- ========================================== -->
            <!-- 1. CỘT TRÁI: DANH SÁCH CÁC BỘ RÈM TRONG GIỎ -->
            <!-- ========================================== -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-3">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light border-bottom text-muted small text-uppercase">
                                <tr>
                                    <!-- Checkbox Chọn Tất Cả -->
                                    <th class="ps-3 py-3 text-center" style="width: 45px;">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input" checked onchange="toggleSelectAll(this)" title="Chọn tất cả">
                                    </th>
                                    <th class="py-3" style="min-width: 240px;">Sản Phẩm & Kích Thước</th>
                                    <th class="py-3 text-center" style="min-width: 110px;">Đơn Giá / m²</th>
                                    <th class="py-3 text-center" style="min-width: 120px;">Số Lượng</th>
                                    <th class="py-3 text-end pe-3" style="min-width: 120px;">Thành Tiền</th>
                                    <th class="py-3 text-center" style="width: 45px;"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($cartItems as $item)
                                    @php
                                        $effectivePrice = $item->sale_unit_price ?? $item->unit_price;
                                        $itemTotal = $item->area * $effectivePrice * $item->quantity;
                                        $itemTotalArea = $item->area * $item->quantity;
                                    @endphp
                                    <tr class="border-bottom cart-item-row" id="row_item_{{ $item->id }}">
                                        <!-- Checkbox từng món -->
                                        <td class="ps-3 py-3 text-center">
                                            <input type="checkbox" class="form-check-input cart-item-checkbox" 
                                                   value="{{ $item->id }}" 
                                                   data-price="{{ $itemTotal }}" 
                                                   data-area="{{ $itemTotalArea }}" 
                                                   data-qty="{{ $item->quantity }}" 
                                                   checked 
                                                   onchange="recalculateCartSelection()">
                                        </td>

                                        <!-- Sản phẩm & Chi tiết may đo -->
                                        <td class="py-3">
                                            <div class="d-flex gap-3 align-items-center">
                                                <a href="{{ route('products.show', $item->product->slug) }}">
                                                    <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" 
                                                         class="rounded-3 border object-fit-cover shadow-sm" style="width: 65px; height: 65px;">
                                                </a>
                                                <div>
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="fw-bold text-dark text-decoration-none d-block mb-1" style="font-size: 0.95rem;">
                                                        {{ $item->product->name }}
                                                    </a>
                                                    
                                                    <!-- Màu sắc đã chọn -->
                                                    <div class="d-flex align-items-center gap-1 mb-1">
                                                        <span class="small text-muted">Màu:</span>
                                                        @if ($item->color)
                                                            <span class="rounded-circle border d-inline-block" style="width: 13px; height: 13px; background-color: {{ $item->color->hex_code ?? '#ccc' }};"></span>
                                                            <strong class="small text-dark">{{ $item->color->name }}</strong>
                                                        @else
                                                            <span class="small text-muted">Mặc định</span>
                                                        @endif
                                                    </div>

                                                    <!-- Kích thước may đo & Diện tích -->
                                                    <div class="small bg-light px-2 py-1 rounded d-inline-block border" style="font-size: 0.78rem;">
                                                        <i class="bi bi-rulers text-warning me-1"></i>
                                                        <span>Kích thước: <strong>{{ number_format($item->width, 2) }}m</strong> (R) × <strong>{{ number_format($item->height, 2) }}m</strong> (C) = <strong class="text-danger">{{ number_format($item->area, 2) }} m²</strong></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Đơn giá / m2 -->
                                        <td class="text-center py-3">
                                            <span class="fw-bold text-dark small">{{ number_format($effectivePrice, 0, ',', '.') }} đ</span>
                                            @if ($item->sale_unit_price)
                                                <small class="text-muted text-decoration-line-through d-block" style="font-size: 0.72rem;">
                                                    {{ number_format($item->unit_price, 0, ',', '.') }} đ
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Cập nhật số lượng (+ / -) -->
                                        <td class="text-center py-3">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex align-items-center border rounded-pill p-1 bg-light">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <span class="px-2 fw-bold small text-dark">{{ $item->quantity }}</span>
                                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="btn btn-sm btn-link text-dark p-0 px-2 text-decoration-none">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Thành tiền từng bộ -->
                                        <td class="text-end pe-3 py-3">
                                            <span class="fw-extrabold text-danger fs-6">
                                                {{ number_format($itemTotal, 0, ',', '.') }} đ
                                            </span>
                                        </td>

                                        <!-- Nút xóa -->
                                        <td class="text-center py-3">
                                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có muốn bỏ bộ rèm này khỏi giỏ hàng?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle p-1" title="Xóa món này">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Tiếp tục chọn thêm mẫu rèm
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. CỘT PHẢI: TỔNG KẾT ĐƠN HÀNG & THANH TOÁN -->
            <!-- ========================================== -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px; z-index: 10;">
                    <h5 class="fw-bold text-dark pb-3 border-bottom mb-3">Tóm Tắt Đơn Hàng</h5>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Đã chọn thanh toán:</span>
                        <strong class="text-dark" id="selectedCountDisplay">{{ $cartItems->sum('quantity') }} bộ</strong>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Tổng diện tích vải may:</span>
                        <strong class="text-dark" id="selectedAreaDisplay">{{ number_format($totalArea, 2) }} m²</strong>
                    </div>

                    <!-- KHU VỰC VOUCHER / MÃ GIẢM GIÁ TẠI GIỎ HÀNG -->
                    <div class="card border border-warning bg-warning bg-opacity-10 rounded-3 p-3 mb-3">
                        <label for="cart_voucher_input" class="form-label fw-bold small text-dark d-flex align-items-center justify-content-between mb-2">
                            <span><i class="bi bi-ticket-perforated-fill text-warning me-1"></i> Mã Giảm Giá</span>
                            <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">ƯU ĐÃI</span>
                        </label>
                        
                        <div class="input-group mb-2">
                            <input type="text" id="cart_voucher_input" class="form-control text-uppercase font-monospace form-control-sm" 
                                   placeholder="Nhập mã (VD: REMMOI50K)" value="{{ $appliedVoucher?->code ?? '' }}" {{ $appliedVoucher ? 'readonly' : '' }}>
                            <button class="btn btn-dark btn-sm fw-bold px-3" type="button" id="cartApplyVoucherBtn" style="{{ $appliedVoucher ? 'display:none;' : '' }}">
                                Áp Dụng
                            </button>
                            <button class="btn btn-outline-danger btn-sm" type="button" id="cartRemoveVoucherBtn" style="{{ $appliedVoucher ? '' : 'display:none;' }}" title="Gỡ mã này">
                                <i class="bi bi-x-lg"></i> Gỡ
                            </button>
                        </div>

                        <div id="cartVoucherMessage" class="small {{ $appliedVoucher ? 'text-success fw-bold' : 'd-none' }}">
                            @if($appliedVoucher)
                                <i class="bi bi-check-circle-fill me-1"></i> Đã áp dụng mã {{ $appliedVoucher->code }} ({{ $appliedVoucher->discount_label }})
                            @endif
                        </div>

                        <!-- Gợi ý mã có sẵn -->
                        <div class="mt-2 pt-2 border-top border-warning border-opacity-25">
                            <span class="text-muted small d-block mb-1" style="font-size: 0.75rem;">Mã gợi ý:</span>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="cartQuickApplyVoucher('REMMOI50K')">
                                    REMMOI50K (-50k)
                                </button>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="cartQuickApplyVoucher('VIPREM10')">
                                    VIPREM10 (-10%)
                                </button>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 font-monospace text-dark" style="font-size: 0.72rem;" onclick="cartQuickApplyVoucher('GIAM100K')">
                                    GIAM100K (-100k)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dòng Giảm Giá Voucher -->
                    <div id="cartVoucherDiscountRow" class="d-flex justify-content-between small text-danger mb-2 {{ $voucherDiscount > 0 ? '' : 'd-none' }}">
                        <span><i class="bi bi-tag-fill me-1"></i>Giảm giá Voucher:</span>
                        <strong id="cart_voucher_discount_text">-{{ number_format($voucherDiscount, 0, ',', '.') }} đ</strong>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <span>Phí khảo sát & phụ kiện ray:</span>
                        <span class="text-success fw-bold">MIỄN PHÍ</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                        <span class="fw-bold text-dark fs-6">TỔNG TẠM TÍNH:</span>
                        <h4 class="fw-extrabold text-danger mb-0" id="selectedSubtotalDisplay">
                            {{ number_format(max(0, $subtotal - $voucherDiscount), 0, ',', '.') }} đ
                        </h4>
                    </div>

                    <!-- Thông báo khi chưa chọn món nào -->
                    <div id="noSelectionAlert" class="alert alert-warning py-2 px-3 small d-none mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng tích chọn ít nhất 1 bộ rèm để tiến hành đặt hàng.
                    </div>

                    <!-- Nút tiến hành thanh toán -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('checkout.index') }}" id="checkoutBtn" class="btn btn-gold py-3 fw-bold shadow">
                            <i class="bi bi-credit-card me-2"></i> TIẾN HÀNH ĐẶT HÀNG (<span id="btnSelectedQty">{{ $cartItems->sum('quantity') }}</span>)
                        </a>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 small text-secondary">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-truck text-warning fs-6"></i>
                            <span>Miễn phí vận chuyển & lắp đặt nội thành.</span>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-success fs-6"></i>
                            <span>Bảo hành phụ kiện 3 năm, đổi mới 1-1.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    let cartVoucherDiscount = {{ (int) $voucherDiscount }};
    let currentCartRawSubtotal = {{ (int) $subtotal }};

    function cartQuickApplyVoucher(code) {
        const input = document.getElementById('cart_voucher_input');
        if (input) {
            input.value = code;
            document.getElementById('cartApplyVoucherBtn')?.click();
        }
    }

    // 1. Chọn / Bỏ chọn tất cả checkbox
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCheckbox.checked;
        });
        recalculateCartSelection();
    }

    // 2. Tính toán lại tổng tiền, số lượng, diện tích của các món ĐÃ TÍCH CHỌN
    function recalculateCartSelection() {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        const masterCheckbox = document.getElementById('selectAllCheckbox');
        
        let totalQty = 0;
        let totalArea = 0;
        let totalPrice = 0;
        let checkedCount = 0;

        checkboxes.forEach(cb => {
            const row = cb.closest('.cart-item-row');
            if (cb.checked) {
                checkedCount++;
                totalQty += parseInt(cb.getAttribute('data-qty')) || 0;
                totalArea += parseFloat(cb.getAttribute('data-area')) || 0;
                totalPrice += parseFloat(cb.getAttribute('data-price')) || 0;
                row.classList.remove('opacity-50');
            } else {
                row.classList.add('opacity-50');
            }
        });

        currentCartRawSubtotal = totalPrice;

        // Cập nhật trạng thái checkbox "Chọn tất cả"
        if (masterCheckbox) {
            masterCheckbox.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
        }

        // Cập nhật hiển thị số liệu
        document.getElementById('selectedCountDisplay').innerText = totalQty + ' bộ';
        document.getElementById('selectedAreaDisplay').innerText = totalArea.toFixed(2) + ' m²';
        
        const finalSubtotal = Math.max(0, totalPrice - cartVoucherDiscount);
        document.getElementById('selectedSubtotalDisplay').innerText = new Intl.NumberFormat('vi-VN').format(finalSubtotal) + ' đ';
        document.getElementById('btnSelectedQty').innerText = totalQty;

        const discountRow = document.getElementById('cartVoucherDiscountRow');
        const discountText = document.getElementById('cart_voucher_discount_text');
        if (cartVoucherDiscount > 0) {
            discountRow.classList.remove('d-none');
            discountText.innerText = '-' + new Intl.NumberFormat('vi-VN').format(cartVoucherDiscount) + ' đ';
        } else {
            discountRow.classList.add('d-none');
        }

        // Bật / Tắt nút Đặt hàng nếu không chọn món nào
        const checkoutBtn = document.getElementById('checkoutBtn');
        const alertBox = document.getElementById('noSelectionAlert');
        if (checkedCount === 0) {
            checkoutBtn.classList.add('disabled', 'opacity-50');
            alertBox.classList.remove('d-none');
        } else {
            checkoutBtn.classList.remove('disabled', 'opacity-50');
            alertBox.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const cartVoucherInput = document.getElementById('cart_voucher_input');
        const cartApplyVoucherBtn = document.getElementById('cartApplyVoucherBtn');
        const cartRemoveVoucherBtn = document.getElementById('cartRemoveVoucherBtn');
        const cartVoucherMessage = document.getElementById('cartVoucherMessage');

        if (cartApplyVoucherBtn) {
            cartApplyVoucherBtn.addEventListener('click', function () {
                const code = cartVoucherInput.value.trim();
                if (!code) {
                    cartVoucherMessage.className = 'small text-danger';
                    cartVoucherMessage.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng nhập mã giảm giá.';
                    cartVoucherMessage.classList.remove('d-none');
                    return;
                }

                cartApplyVoucherBtn.disabled = true;
                cartApplyVoucherBtn.innerText = 'Đang kiểm tra...';

                fetch("{{ route('vouchers.apply') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    cartApplyVoucherBtn.disabled = false;
                    cartApplyVoucherBtn.innerText = 'Áp Dụng';

                    if (body.success) {
                        cartVoucherDiscount = parseInt(body.discount_amount) || 0;
                        cartVoucherMessage.className = 'small text-success fw-bold';
                        cartVoucherMessage.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${body.message}`;
                        cartVoucherMessage.classList.remove('d-none');

                        cartVoucherInput.readOnly = true;
                        cartApplyVoucherBtn.style.display = 'none';
                        cartRemoveVoucherBtn.style.display = 'inline-block';

                        recalculateCartSelection();
                    } else {
                        cartVoucherMessage.className = 'small text-danger';
                        cartVoucherMessage.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${body.message}`;
                        cartVoucherMessage.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    cartApplyVoucherBtn.disabled = false;
                    cartApplyVoucherBtn.innerText = 'Áp Dụng';
                    console.error("Lỗi áp dụng voucher:", err);
                });
            });
        }

        if (cartRemoveVoucherBtn) {
            cartRemoveVoucherBtn.addEventListener('click', function () {
                fetch("{{ route('vouchers.remove') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(body => {
                    cartVoucherDiscount = 0;
                    cartVoucherInput.value = '';
                    cartVoucherInput.readOnly = false;
                    cartApplyVoucherBtn.style.display = 'inline-block';
                    cartRemoveVoucherBtn.style.display = 'none';

                    cartVoucherMessage.className = 'small text-muted';
                    cartVoucherMessage.innerHTML = '<i class="bi bi-info-circle me-1"></i> Đã gỡ bỏ mã giảm giá.';
                    recalculateCartSelection();
                })
                .catch(err => console.error("Lỗi gỡ voucher:", err));
            });
        }

        recalculateCartSelection();
    });
</script>
@endpush
@endsection
