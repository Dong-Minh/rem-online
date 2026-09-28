@extends('layouts.admin')

@section('title', 'Báo Cáo Thống Kê & Phân Tích Doanh Thu')
@section('page_title', 'Báo Cáo & Phân Tích Doanh Thu Toàn Diện')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Báo Cáo Hiệu Quả Kinh Doanh & May Đo</h4>
            <p class="text-muted mb-0 small">Theo dõi doanh thu theo mét vuông ($m^2$), cơ cấu sản phẩm và hiệu quả các chiến dịch khuyến mãi.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Year Selector Filter -->
            <form method="GET" action="{{ route('admin.analytics.index') }}" class="d-flex align-items-center gap-2">
                <select name="year" class="form-select bg-white border shadow-sm rounded-3" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </form>

            <!-- Export CSV/Excel Button -->
            <a href="{{ route('admin.analytics.export') }}" class="btn btn-success shadow-sm rounded-3 d-flex align-items-center gap-2 px-3">
                <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                <span class="fw-semibold">Xuất File Excel (CSV)</span>
            </a>
        </div>
    </div>

    <!-- 4 Thẻ KPI Chỉ Số Tài Chính & Sản Xuất -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Tổng Doanh Thu -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Doanh Thu Thực Nhận</div>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($kpis['total_revenue']) }} <small class="fs-6">₫</small></h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Từ {{ number_format($kpis['total_orders']) }} đơn may đo hoàn tất</small>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Tổng Diện Tích May Đo (m2) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Tổng Diện Tích Vải Rèm</div>
                        <h3 class="fw-bold mb-0 mt-1 text-primary">{{ number_format($kpis['total_area_m2'], 2) }} <small class="fs-6">m²</small></h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Sản lượng xưởng may gia công</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-rulers fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Tỷ Lệ Chốt Đơn Khảo Sát -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Tỷ Lệ Chốt Khảo Sát</div>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">{{ $kpis['conversion_rate'] }}%</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Trên tổng {{ $kpis['total_consultations'] }} lịch hẹn tại nhà</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-person-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Giá Trị Đơn Trung Bình (AOV) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom h-100 p-3 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Giá Trị Trung Bình / Đơn</div>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">{{ number_format($kpis['average_order_value']) }} <small class="fs-6">₫</small></h3>
                        <small class="text-muted" style="font-size: 0.75rem;">AOV (Average Order Value)</small>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng Biểu Đồ 1: Doanh Thu 12 Tháng & Cơ Cấu Danh Mục -->
    <div class="row g-4 mb-4">
        <!-- Biểu Đồ Cột & Đường: Doanh Thu 12 Tháng -->
        <div class="col-12 col-xl-8">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-graph-up-arrow text-warning me-2"></i>Biến Động Doanh Thu & Khuyến Mãi Năm {{ $selectedYear }}
                        </h6>
                        <small class="text-muted">Doanh thu thực thu so với số tiền đã giảm giá qua Voucher</small>
                    </div>
                    <span class="badge bg-light text-dark border">Đơn vị: VNĐ</span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div style="height: 320px; position: relative;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu Đồ Tròn: Cơ Cấu Doanh Thu Theo Danh Mục -->
        <div class="col-12 col-xl-4">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-pie-chart text-warning me-2"></i>Tỷ Trọng Theo Danh Mục Rèm
                    </h6>
                    <small class="text-muted">Cơ cấu đóng góp doanh thu của các dòng rèm</small>
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-center">
                    <div style="height: 250px; position: relative;" class="mb-3">
                        <canvas id="categoryRevenueChart"></canvas>
                    </div>
                    <div class="text-center small text-muted">
                        Dữ liệu được tính trên toàn bộ đơn may đo không bị hủy.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng Biểu Đồ 2 & Phân Bổ Trạng Thái Đơn Hàng -->
    <div class="row g-4 mb-4">
        <!-- Phân Bổ Trạng Thái Đơn Hàng -->
        <div class="col-12 col-lg-5">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-pie-chart-fill text-warning me-2"></i>Phân Bổ Trạng Thái Đơn Hàng
                    </h6>
                    <small class="text-muted">Tỷ lệ đơn may đo hoàn tất, đang xử lý và đã hủy</small>
                </div>
                <div class="card-body p-4">
                    <div style="height: 220px; position: relative;" class="mb-4">
                        <canvas id="orderStatusChart"></canvas>
                    </div>

                    <div class="row g-2">
                        @foreach($orderStatuses as $statusKey => $st)
                            <div class="col-6">
                                <div class="p-2 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="rounded-circle d-inline-block" style="width: 12px; height: 12px; background-color: {{ $st['color'] }};"></span>
                                        <span class="small fw-semibold text-dark">{{ $st['label'] }}</span>
                                    </div>
                                    <span class="badge bg-white text-dark border fw-bold">{{ $st['count'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Báo Cáo Hiệu Quả Voucher Khuyến Mãi -->
        <div class="col-12 col-lg-7">
            <div class="card card-custom h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-ticket-perforated text-warning me-2"></i>Hiệu Quả Mã Giảm Giá (Vouchers)
                        </h6>
                        <small class="text-muted">Các chiến dịch kích cầu may rèm mang lại chuyển đổi cao nhất</small>
                    </div>
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-sm btn-outline-dark rounded-pill">Quản lý Voucher &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Voucher</th>
                                <th>Loại Giảm</th>
                                <th class="text-center">Lượt Áp Dụng</th>
                                <th class="text-end">Tổng Tiết Kiệm Khách Hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($voucherStats as $v)
                                <tr>
                                    <td>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger font-monospace px-2 py-1 fs-6">
                                            {{ $v->code }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($v->discount_type === 'percent')
                                            <span class="small fw-semibold text-primary">Giảm {{ $v->discount_value }}%</span>
                                        @else
                                            <span class="small fw-semibold text-success">Giảm {{ number_format($v->discount_value) }}₫</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-dark">
                                        {{ $v->usages_count }} lần
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($v->total_discount_given ?? 0) }} ₫
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        Chưa có mã giảm giá nào được sử dụng.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng Xếp Hạng: Top 5 Mẫu Rèm Bán Chạy Nhất -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-trophy text-warning me-2"></i>Top Mẫu Rèm Bán Chạy Nhất Theo Doanh Thu & Diện Tích
                </h6>
                <small class="text-muted">Các dòng rèm được khách hàng yêu thích và đặt may nhiều nhất</small>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark rounded-pill">Xem tất cả sản phẩm &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">TOP</th>
                        <th>Sản Phẩm Rèm</th>
                        <th>Danh Mục</th>
                        <th class="text-center">Tổng Diện Tích May (m²)</th>
                        <th class="text-center">Số Bộ Đã Đặt</th>
                        <th class="text-end">Tổng Doanh Thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $idx => $prod)
                        <tr>
                            <td class="text-center">
                                @if($idx === 0)
                                    <span class="badge bg-warning text-dark rounded-circle p-2 fs-6">🥇</span>
                                @elseif($idx === 1)
                                    <span class="badge bg-secondary text-white rounded-circle p-2 fs-6">🥈</span>
                                @elseif($idx === 2)
                                    <span class="badge bg-danger text-white rounded-circle p-2 fs-6">🥉</span>
                                @else
                                    <span class="text-muted fw-bold">{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" class="rounded-3 border object-fit-cover" style="width: 48px; height: 48px; min-width: 48px;">
                                    <div>
                                        <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-gold d-block text-truncate" style="max-width: 280px;">
                                            {{ $prod->name }}
                                        </a>
                                        <small class="text-muted font-monospace">SKU: {{ $prod->sku }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ $prod->categories->first()->name ?? 'Rèm cao cấp' }}
                                </span>
                            </td>
                            <td class="text-center fw-bold text-primary">
                                {{ number_format($prod->total_sold_area ?? 0, 2) }} m²
                            </td>
                            <td class="text-center fw-semibold text-dark">
                                {{ number_format($prod->total_sold_quantity ?? 0) }} bộ
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">
                                {{ number_format($prod->total_revenue ?? 0) }} ₫
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                Chưa có số liệu đơn may đo hoàn tất.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js 4.4 CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. BIỂU ĐỒ DOANH THU 12 THÁNG
        const ctxRevenue = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthLabels) !!},
                datasets: [
                    {
                        type: 'line',
                        label: 'Doanh Thu Thực Thu (VNĐ)',
                        data: {!! json_encode($monthlyRevenueData) !!},
                        borderColor: '#b8860b',
                        backgroundColor: 'rgba(184, 134, 11, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#b8860b',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Số Tiền Giảm Giá Voucher (VNĐ)',
                        data: {!! json_encode($monthlyDiscountData) !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 14,
                            usePointStyle: true,
                            font: {
                                family: 'Plus Jakarta Sans',
                                weight: 600
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫';
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // 2. BIỂU ĐỒ CƠ CẤU DANH MỤC (DOUGHNUT)
        const ctxCategory = document.getElementById('categoryRevenueChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryLabels) !!},
                datasets: [{
                    data: {!! json_encode($categoryRevenueData) !!},
                    backgroundColor: {!! json_encode($categoryColors) !!},
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: {
                                size: 11,
                                family: 'Plus Jakarta Sans'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${context.label}: ${new Intl.NumberFormat('vi-VN').format(value)} ₫ (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // 3. BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (PIE)
        const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_column($orderStatuses, 'label')) !!},
                datasets: [{
                    data: {!! json_encode(array_column($orderStatuses, 'count')) !!},
                    backgroundColor: {!! json_encode(array_column($orderStatuses, 'color')) !!},
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
