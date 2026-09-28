@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn hàng
    </a>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam text-warning me-2"></i>Đơn Hàng: <span class="font-monospace text-primary">{{ $order->order_code }}</span>
            </h4>
            <span class="text-muted small">
                Ngày đặt: <strong>{{ $order->created_at->format('d/m/Y H:i:s') }}</strong> • Khách: <strong>{{ $order->user?->name ?? $order->recipient_name }}</strong>
            </span>
        </div>

        <div class="d-flex gap-2">
            <!-- Nút In Phiếu May Đo A4 -->
            <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-outline-dark">
                <i class="bi bi-printer me-1"></i> In Phiếu May Đo (A4)
            </a>

            <!-- Nút Đồng bộ GHN nếu có mã -->
            @if ($order->ghn_order_code)
                <form action="{{ route('admin.orders.syncGHN', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning text-dark" title="Kiểm tra trạng thái mới nhất từ GHN">
                        <i class="bi bi-arrow-repeat me-1"></i> Đồng Bộ GHN
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- ========================================== -->
    <!-- 1. CỘT TRÁI: DANH SÁCH RÈM MAY ĐO & CHI PHÍ -->
    <!-- ========================================== -->
    <div class="col-12 col-lg-8">
        <!-- Banner trạng thái GHN -->
        @if ($order->ghn_order_code)
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-white text-dark small fw-bold mb-2">VẬN ĐƠN GIAO HÀNG NHANH (GHN)</span>
                        <h4 class="fw-bold mb-1 font-monospace">{{ $order->ghn_order_code }}</h4>
                        <p class="small text-white text-opacity-75 mb-0">
                            Cước vận chuyển thực tế: <strong>{{ number_format($order->ghn_total_fee, 0, ',', '.') }} đ</strong>
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                            {!! $order->shipping_status_badge !!}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bảng danh sách các bộ rèm may đo -->
        <div class="card card-custom overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-scissors text-warning me-1"></i> Danh Sách Rèm May Đo ({{ $order->items->sum('quantity') }} bộ)
                </h6>
                <span class="badge bg-light text-dark border">
                    Tổng diện tích: <strong>{{ number_format($order->items->sum('area'), 2) }} m²</strong>
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="min-width: 250px;">Mẫu Rèm & Thông Số May</th>
                            <th class="py-3 text-center">Đơn Giá / m²</th>
                            <th class="py-3 text-center">Số Lượng</th>
                            <th class="py-3 text-end pe-4">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->product && $item->product->main_image_url)
                                            <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product_name }}" 
                                                 class="rounded-3 border object-fit-cover shadow-sm" style="width: 60px; height: 60px;">
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark mb-1">{{ $item->product_name }}</div>
                                            <div class="small text-muted mb-1">
                                                Màu sắc: <strong>{{ $item->color_name ?? 'Mặc định' }}</strong> • SKU: <code>{{ $item->product_sku }}</code>
                                            </div>
                                            <div class="small bg-light px-2 py-1 rounded d-inline-block border">
                                                <i class="bi bi-rulers text-warning me-1"></i>
                                                Kích thước may: <strong>{{ number_format($item->width, 2) }}m</strong> (Rộng) × <strong>{{ number_format($item->height, 2) }}m</strong> (Cao) = <strong class="text-danger">{{ number_format($item->area, 2) }} m²</strong>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="fw-bold text-dark">{{ number_format($item->sale_unit_price ?? $item->unit_price, 0, ',', '.') }} đ</span>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-2">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <span class="fw-extrabold text-danger fs-6">
                                        {{ number_format($item->item_total, 0, ',', '.') }} đ
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ghi chú đơn hàng của khách -->
        @if ($order->note)
            <div class="card card-custom p-3 bg-light mb-4">
                <span class="small fw-bold text-dark d-block mb-1"><i class="bi bi-chat-left-quote text-warning me-1"></i> Ghi Chú Của Khách Hàng:</span>
                <p class="small text-muted mb-0 font-monospace">{{ $order->note }}</p>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- 2. CỘT PHẢI: XỬ LÝ ĐƠN & CHI TIẾT THANH TOÁN -->
    <!-- ========================================== -->
    <div class="col-12 col-lg-4">
        <!-- Form cập nhật trạng thái đơn hàng -->
        <div class="card card-custom p-4 mb-4 border-start border-4 border-warning">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-sliders text-warning me-1"></i> Cập Nhật Trạng Thái Đơn
            </h6>

            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <!-- Trạng thái đơn hàng -->
                <div class="mb-3">
                    <label for="status" class="form-label small fw-bold">Trạng Thái Đơn Hàng <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xác nhận (Pending)</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận (Confirmed)</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Đang may đo tại xưởng (Preparing)</option>
                        <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao hàng (Shipping)</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã giao thành công (Delivered)</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn tất đơn hàng (Completed)</option>
                        @if (in_array($order->status, ['shipping', 'delivered', 'completed'], true) || in_array($order->shipping_status, ['delivering', 'delivered'], true))
                            <option value="cancelled" disabled>🚫 Hủy đơn hàng (Không khả dụng khi đang giao)</option>
                        @else
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng (Cancelled)</option>
                        @endif
                    </select>
                </div>

                <!-- Trạng thái thanh toán -->
                <div class="mb-3">
                    <label for="payment_status" class="form-label small fw-bold">Trạng Thái Thanh Toán <span class="text-danger">*</span></label>
                    <select class="form-select" id="payment_status" name="payment_status" required>
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    </select>
                </div>

                <!-- Trạng thái GHN -->
                <div class="mb-3">
                    <label for="shipping_status" class="form-label small fw-bold">Trạng Thái Giao Nhận</label>
                    <select class="form-select" id="shipping_status" name="shipping_status">
                        <option value="not_shipped" {{ $order->shipping_status === 'not_shipped' ? 'selected' : '' }}>Chưa gửi GHN</option>
                        <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Chờ GHN lấy hàng</option>
                        <option value="ready_to_pick" {{ $order->shipping_status === 'ready_to_pick' ? 'selected' : '' }}>GHN chuẩn bị lấy</option>
                        <option value="delivering" {{ $order->shipping_status === 'delivering' ? 'selected' : '' }}>GHN đang giao</option>
                        <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>GHN đã giao</option>
                        <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>Đã hủy vận đơn</option>
                    </select>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-gold py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> Cập Nhật Đơn Hàng
                    </button>
                </div>
            </form>
        </div>

        <!-- Thông tin người nhận -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark pb-2 border-bottom mb-3">
                <i class="bi bi-geo-alt-fill text-warning me-1"></i> Thông Tin Nhận Hàng
            </h6>
            <div class="mb-2">
                <strong class="text-dark">{{ $order->recipient_name }}</strong>
            </div>
            <div class="small text-muted mb-2">
                <i class="bi bi-telephone me-1"></i> {{ $order->recipient_phone }}
            </div>
            @if ($order->recipient_email)
                <div class="small text-muted mb-2">
                    <i class="bi bi-envelope me-1"></i> {{ $order->recipient_email }}
                </div>
            @endif
            <div class="small text-secondary mb-0">
                <i class="bi bi-house-door me-1"></i> {{ $order->shipping_address }}
            </div>
        </div>

        <!-- Tóm tắt chi phí thanh toán -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark pb-2 border-bottom mb-3">
                <i class="bi bi-receipt text-warning me-1"></i> Chi Tiết Thanh Toán
            </h6>

            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>Tiền hàng rèm may:</span>
                <strong class="text-dark">{{ number_format($order->subtotal, 0, ',', '.') }} đ</strong>
            </div>

            @if ($order->voucher_discount > 0)
                <div class="d-flex justify-content-between small text-danger mb-2">
                    <span>
                        <i class="bi bi-tag-fill me-1"></i>Giảm giá Voucher ({{ $order->voucher?->code ?? 'Mã' }}):
                    </span>
                    <strong>-{{ number_format($order->voucher_discount, 0, ',', '.') }} đ</strong>
                </div>
            @endif

            <div class="d-flex justify-content-between small text-muted mb-2">
                <span>Cước vận chuyển (GHN):</span>
                <strong class="text-dark">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</strong>
            </div>

            <div class="d-flex justify-content-between small text-muted mb-3">
                <span>Hình thức:</span>
                <span class="badge bg-light text-dark border">{{ $order->payment_method_name }}</span>
            </div>

            <hr class="my-3">

            <div class="d-flex justify-content-between align-items-baseline mb-0">
                <span class="fw-bold text-dark">TỔNG THANH TOÁN:</span>
                <h4 class="fw-extrabold text-danger mb-0">{{ number_format($order->total, 0, ',', '.') }} VNĐ</h4>
            </div>
        </div>
    </div>
</div>
@endsection
