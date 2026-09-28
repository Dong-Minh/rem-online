@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục')
@section('page_title', 'Danh Mục Rèm Cửa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Quản lý các phân loại rèm theo kiểu dáng, không gian, phong cách và công dụng.</p>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-circle me-1"></i> Thêm Danh Mục Mới
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">Thứ Tự</th>
                    <th style="width: 80px;">Ảnh</th>
                    <th>Tên Danh Mục</th>
                    <th>Đường Dẫn (Slug)</th>
                    <th>Danh Mục Cha</th>
                    <th>Số Sản Phẩm</th>
                    <th>Hiện Trang Chủ</th>
                    <th>Trạng Thái</th>
                    <th class="text-end" style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                    <tr>
                        <td class="fw-bold text-muted text-center">{{ $cat->sort_order }}</td>
                        <td>
                            @if ($cat->image)
                                <img src="{{ Str::startsWith($cat->image, ['http://', 'https://']) ? $cat->image : asset('storage/' . $cat->image) }}" 
                                     alt="{{ $cat->name }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cat->name }}</div>
                            @if ($cat->description)
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $cat->description }}</small>
                            @endif
                        </td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td>
                            @if ($cat->parent)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $cat->parent->name }}</span>
                            @else
                                <span class="badge bg-light text-muted border">Danh mục gốc</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $cat->products_count }} sản phẩm</span>
                        </td>
                        <td>
                            @if ($cat->show_on_home)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Có</span>
                            @else
                                <span class="badge bg-light text-muted">Không</span>
                            @endif
                        </td>
                        <td>
                            @if ($cat->is_active)
                                <span class="badge bg-success">Đang hiện</span>
                            @else
                                <span class="badge bg-secondary">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary me-1" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
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
                        <td colspan="9" class="text-center py-5 text-muted">Chưa có danh mục nào. Hãy tạo danh mục đầu tiên!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($categories->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
