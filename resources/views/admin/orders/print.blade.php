<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Phiếu May Đo Đơn Hàng {{ $order->order_code }} — Rèm Online</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background: #fff;
            font-size: 14px;
        }

        .invoice-box {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }

        .invoice-header {
            border-bottom: 2px solid #b8860b;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .table-custom th {
            background-color: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
        }

        .signature-box {
            margin-top: 50px;
            text-align: center;
        }

        .signature-space {
            height: 80px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-box {
                border: none;
                padding: 0;
                max-width: 100%;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <!-- Nút hành động không in -->
        <div class="no-print d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-secondary btn-sm">
                ← Quay lại chi tiết đơn
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                🖨️ In Phiếu Này (Print)
            </button>
        </div>

        <div class="invoice-box shadow-sm rounded-3">
            <!-- Header -->
            <div class="invoice-header d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.02em;">RÈM ONLINE</h3>
                    <div class="text-muted small">Cửa Hàng Rèm Cửa & Nội Thất May Đo Cao Cấp</div>
                    <div class="text-muted small">Hotline: <strong>0901.234.567</strong> • Website: www.remonline.vn</div>
                    <div class="text-muted small">Xưởng may: 322/76 Ngõ 322 Mỹ Đình, Q. Nam Từ Liêm, Hà Nội</div>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold text-danger font-monospace mb-1">{{ $order->order_code }}</h5>
                    <div class="small text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                    @if ($order->ghn_order_code)
                        <div class="small mt-1">
                            Mã GHN: <strong class="font-monospace text-dark">{{ $order->ghn_order_code }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tiêu đề phiếu -->
            <div class="text-center mb-4">
                <h4 class="fw-bold text-uppercase mb-1">PHIẾU CẮT MAY & BÀN GIAO ĐƠN HÀNG</h4>
                <p class="text-muted small mb-0">(Dùng cho bộ phận xưởng may đo, đóng gói & giao nhận)</p>
            </div>

            <!-- Thông tin khách hàng & Giao hàng -->
            <div class="row g-3 mb-4 p-3 bg-light rounded-3">
                <div class="col-6">
                    <span class="text-muted small d-block">Khách hàng nhận rèm:</span>
                    <strong class="text-dark fs-6">{{ $order->recipient_name }}</strong>
                    <div class="small mt-1">Số điện thoại: <strong>{{ $order->recipient_phone }}</strong></div>
                    @if ($order->recipient_email)
                        <div class="small">Email: {{ $order->recipient_email }}</div>
                    @endif
                </div>
                <div class="col-6">
                    <span class="text-muted small d-block">Địa chỉ giao hàng & lắp đặt:</span>
                    <strong class="text-dark">{{ $order->shipping_address }}</strong>
                    @if ($order->note)
                        <div class="small mt-1 text-danger">
                            Ghi chú: <em>"{{ $order->note }}"</em>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bảng thông số may đo từng bộ rèm -->
            <h6 class="fw-bold text-dark mb-2">DANH SÁCH BỘ RÈM MAY ĐO THEO YÊU CẦU:</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-custom align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">STT</th>
                            <th>Tên Mẫu Rèm & Màu Sắc</th>
                            <th class="text-center">Kích Thước May (R × C)</th>
                            <th class="text-center">Diện Tích</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Đơn Giá/m²</th>
                            <th class="text-end">Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $index => $item)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                    <small class="text-muted">Màu: <strong>{{ $item->color_name ?? 'Mặc định' }}</strong> • SKU: <code>{{ $item->product_sku }}</code></small>
                                </td>
                                <td class="text-center font-monospace fw-bold">
                                    {{ number_format($item->width, 2) }}m × {{ number_format($item->height, 2) }}m
                                </td>
                                <td class="text-center fw-bold text-danger font-monospace">
                                    {{ number_format($item->area, 2) }} m²
                                </td>
                                <td class="text-center fw-bold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="text-end font-monospace">
                                    {{ number_format($item->sale_unit_price ?? $item->unit_price, 0, ',', '.') }} đ
                                </td>
                                <td class="text-end fw-bold text-dark font-monospace">
                                    {{ number_format($item->item_total, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tổng số lượng rèm:</td>
                            <td class="text-center fw-bold text-danger">{{ $order->items->sum('quantity') }} bộ</td>
                            <td class="text-end fw-bold">Tiền hàng:</td>
                            <td class="text-end fw-bold font-monospace">{{ number_format($order->subtotal, 0, ',', '.') }} đ</td>
                        </tr>
                        @if ($order->voucher_discount > 0)
                            <tr>
                                <td colspan="6" class="text-end fw-bold text-danger">Giảm giá voucher ({{ $order->voucher?->code ?? '' }}):</td>
                                <td class="text-end fw-bold text-danger font-monospace">-{{ number_format($order->voucher_discount, 0, ',', '.') }} đ</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="6" class="text-end fw-bold">Cước vận chuyển (GHN):</td>
                            <td class="text-end fw-bold font-monospace">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="6" class="text-end fw-extrabold fs-6">TỔNG CỘNG THANH TOÁN:</td>
                            <td class="text-end fw-extrabold text-danger fs-6 font-monospace">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Chữ ký các bên -->
            <div class="row signature-box">
                <div class="col-4">
                    <strong class="d-block">Người Lập Phiếu</strong>
                    <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                    <div class="signature-space"></div>
                    <span class="fw-bold">{{ auth()->user()->name ?? 'Ban Quản Trị' }}</span>
                </div>
                <div class="col-4">
                    <strong class="d-block">Thợ May / Xưởng Cắt Vải</strong>
                    <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                    <div class="signature-space"></div>
                </div>
                <div class="col-4">
                    <strong class="d-block">Khách Hàng Nhận Rèm</strong>
                    <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                    <div class="signature-space"></div>
                    <span class="fw-bold">{{ $order->recipient_name }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
