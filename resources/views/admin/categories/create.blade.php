@extends('layouts.admin')

@section('title', 'Thêm Mới Danh Mục')
@section('page_title', 'Thêm Danh Mục Rèm Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tên danh mục -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Tên Danh Mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                           value="{{ old('name') }}" required placeholder="Ví dụ: Rèm Vải Nhật Bản">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug (tùy chọn) -->
                <div class="mb-3">
                    <label for="slug" class="form-label fw-bold">Đường Dẫn Tĩnh (Slug)</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" 
                           value="{{ old('slug') }}" placeholder="Để trống hệ thống sẽ tự động tạo từ tên">
                    <small class="text-muted">Ví dụ: <code>rem-vai-nhat-ban</code></small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Danh mục cha -->
                <div class="mb-3">
                    <label for="parent_id" class="form-label fw-bold">Danh Mục Cha</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">-- Không có (Danh mục gốc cấp cao nhất) --</option>
                        @foreach ($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô Tả Danh Mục</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" 
                              rows="3" placeholder="Mô tả ngắn về đặc tính rèm trong danh mục này...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ảnh đại diện danh mục -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Ảnh Đại Diện Danh Mục</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                    <small class="text-muted">Định dạng JPG, PNG, WEBP tối đa 2MB.</small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thứ tự & Trạng thái -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="sort_order" class="form-label fw-bold">Thứ Tự Hiển Thị</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                        <small class="text-muted">Số càng nhỏ hiển thị càng trước.</small>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-center pt-md-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="show_on_home" name="show_on_home" value="1" {{ old('show_on_home') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="show_on_home">Hiển thị nổi bật trên Trang Chủ</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Kích hoạt hiển thị</label>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between pt-3 border-top">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="bi bi-check-lg me-1"></i> Lưu Danh Mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
