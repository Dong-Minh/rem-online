@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm Rèm')
@section('page_title', 'Danh Sách Sản Phẩm Rèm')

@section('content')
<!-- Header Actions & Search Filter -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-center">
        <!-- Search Keyword -->
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên rèm, SKU..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Filter by Category -->
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Stock Status -->
        <div class="col-6 col-md-3">
            <select name="stock_status" class="form-select">
                <option value="">-- Trạng thái kho --</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                <option value="hidden" {{ request('stock_status') == 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </div>

        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark flex-grow-1"><i class="bi bi-funnel"></i> Lọc</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" title="Đặt lại"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">Tổng số: <strong>{{ $products->total() }}</strong> sản phẩm rèm</span>
    <a href="{{ route('admin.products.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-circle me-1"></i> Thêm Sản Phẩm Rèm Mới
    </a>
</div>

<!-- Products Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Thông Tin Rèm</th>
                    <th>Mã SKU</th>
                    <th>Đơn Giá / m²</th>
                    <th>Danh Mục</th>
                    <th>Bảng Màu</th>
                    <th>Nhãn Nổi Bật</th>
                    <th>Trạng Thái</th>
                    <th class="text-end" style="width: 130px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $prod)
                    <tr>
                        <td>
                            <img src="{{ $prod->main_image_url }}" alt="{{ $prod->name }}" class="rounded border" style="width: 55px; height: 55px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $prod->name }}</div>
                            <small class="text-muted d-block">{{ $prod->material ?? 'Vải' }} • {{ $prod->curtain_type ?? 'Rèm 2 lớp' }}</small>
                            <small class="text-secondary font-monospace" style="font-size: 0.75rem;">
                                Kích thước: {{ $prod->min_width ?? 1 }}m - {{ $prod->max_width ?? 5 }}m (R) × {{ $prod->min_height ?? 1.5 }}m - {{ $prod->max_height ?? 4 }}m (C)
                            </small>
                        </td>
                        <td><code>{{ $prod->sku }}</code></td>
                        <td>
                            @if ($prod->sale_price)
                                <div class="text-danger fw-bold fs-6">{{ $prod->formatted_sale_price }}</div>
                                <small class="text-muted text-decoration-line-through">{{ $prod->formatted_unit_price }}</small>
                                <span class="badge bg-danger-subtle text-danger ms-1">-{{ $prod->discount_percentage }}%</span>
                            @else
                                <div class="fw-bold text-dark fs-6">{{ $prod->formatted_unit_price }}</div>
                            @endif
                        </td>
                        <td>
                            @foreach ($prod->categories as $c)
                                <span class="badge bg-light text-dark border mb-1">{{ $c->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap" style="max-width: 100px;">
                                @foreach ($prod->colors as $clr)
                                    <span class="rounded-circle border d-inline-block" 
                                          style="width: 16px; height: 16px; background-color: {{ $clr->hex_code ?? '#ccc' }};" 
                                          title="{{ $clr->name }} (Kho: {{ $clr->pivot->stock_quantity ?? 0 }})"></span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if ($prod->is_hot)
                                <span class="badge bg-danger me-1">🔥 HOT</span>
                            @endif
                            @if ($prod->is_trending)
                                <span class="badge bg-info text-dark me-1">⚡ Thịnh hành</span>
                            @endif
                            @if ($prod->is_featured)
                                <span class="badge bg-warning text-dark me-1">⭐ Nổi bật</span>
                            @endif
                        </td>
                        <td>
                            @if ($prod->stock_status === 'in_stock')
                                <span class="badge bg-success-subtle text-success">Còn hàng</span>
                            @elseif ($prod->stock_status === 'out_of_stock')
                                <span class="badge bg-danger-subtle text-danger">Hết hàng</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.edit', $prod) }}" class="btn btn-sm btn-outline-primary me-1" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $prod) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn chuyển sản phẩm này vào thùng rác?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">Không tìm thấy sản phẩm rèm nào phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($products->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
