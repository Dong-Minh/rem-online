@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm Rèm')
@section('page_title', 'Chỉnh Sửa: ' . $product->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Cột trái: Thông tin chính & Giá m2 -->
                <div class="col-12 col-lg-8">
                    <!-- Thông tin cơ bản -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Thông Tin Cơ Bản</h6>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Tên Sản Phẩm Rèm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="sku" class="form-label fw-bold">Mã Sản Phẩm / SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" 
                                       value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="slug" class="form-label fw-bold">Đường Dẫn Tĩnh (Slug) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" 
                                       value="{{ old('slug', $product->slug) }}" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label fw-bold">Mô Tả Ngắn</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" 
                                      rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="description" class="form-label fw-bold">Mô Tả Chi Tiết Sản Phẩm</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" 
                                      rows="5">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Đơn giá theo m2 & Kích thước -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Đơn Giá Theo m² & Giới Hạn Kích Thước</h6>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="unit_price" class="form-label fw-bold">Đơn Giá Gốc / m² (VNĐ) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1000" class="form-control @error('unit_price') is-invalid @enderror" 
                                           id="unit_price" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required>
                                    <span class="input-group-text">đ / m²</span>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="sale_price" class="form-label fw-bold">Giá Khuyến Mãi / m² (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" step="1000" class="form-control @error('sale_price') is-invalid @enderror" 
                                           id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
                                    <span class="input-group-text">đ / m²</span>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Giới hạn kích thước -->
                        <div class="p-3 bg-light rounded-3">
                            <span class="small fw-bold text-secondary d-block mb-2"><i class="bi bi-rulers me-1"></i> Giới hạn kích thước xưởng nhận may (mét):</span>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-1">Rộng Min (m)</label>
                                    <input type="number" step="0.1" class="form-control form-control-sm" name="min_width" value="{{ old('min_width', $product->min_width) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-1">Rộng Max (m)</label>
                                    <input type="number" step="0.1" class="form-control form-control-sm" name="max_width" value="{{ old('max_width', $product->max_width) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-1">Cao Min (m)</label>
                                    <input type="number" step="0.1" class="form-control form-control-sm" name="min_height" value="{{ old('min_height', $product->min_height) }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-1">Cao Max (m)</label>
                                    <input type="number" step="0.1" class="form-control form-control-sm" name="max_height" value="{{ old('max_height', $product->max_height) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng màu sắc & tồn kho theo màu -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. Bảng Màu Sắc Có Sẵn Của Rèm</h6>
                        <div class="row g-3">
                            @php
                                $selectedColors = $product->colors->pluck('pivot.stock_quantity', 'id')->toArray();
                            @endphp
                            @foreach ($colors as $clr)
                                <div class="col-12 col-sm-6">
                                    <div class="border rounded p-2 d-flex align-items-center justify-content-between">
                                        <div class="form-check d-flex align-items-center gap-2">
                                            <input class="form-check-input" type="checkbox" name="colors[]" value="{{ $clr->id }}" id="color_{{ $clr->id }}"
                                                   {{ in_array($clr->id, old('colors', array_keys($selectedColors))) ? 'checked' : '' }}>
                                            <span class="rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $clr->hex_code ?? '#ccc' }};"></span>
                                            <label class="form-check-label fw-semibold small" for="color_{{ $clr->id }}">
                                                {{ $clr->name }}
                                            </label>
                                        </div>
                                        <div class="d-flex align-items-center gap-1" style="width: 100px;">
                                            <input type="number" name="color_stock[{{ $clr->id }}]" class="form-control form-control-sm text-center" 
                                                   placeholder="Kho" value="{{ old("color_stock.{$clr->id}", $selectedColors[$clr->id] ?? 50) }}" title="Số mét vải">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Phân loại, Ảnh & Nhãn -->
                <div class="col-12 col-lg-4">
                    <!-- Danh mục -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">4. Thuộc Danh Mục <span class="text-danger">*</span></h6>
                        @php
                            $selectedCategories = $product->categories->pluck('id')->toArray();
                        @endphp
                        <div class="overflow-auto" style="max-height: 220px;">
                            @foreach ($categories as $cat)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}"
                                           {{ in_array($cat->id, old('categories', $selectedCategories)) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="cat_{{ $cat->id }}">
                                        {{ $cat->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Đặc tính kỹ thuật -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">5. Đặc Tính Kỹ Thuật</h6>
                        
                        <div class="mb-3">
                            <label for="material" class="form-label small fw-bold">Chất Liệu</label>
                            <input type="text" class="form-control form-control-sm" id="material" name="material" 
                                   value="{{ old('material', $product->material) }}">
                        </div>

                        <div class="mb-3">
                            <label for="style" class="form-label small fw-bold">Phong Cách</label>
                            <input type="text" class="form-control form-control-sm" id="style" name="style" 
                                   value="{{ old('style', $product->style) }}">
                        </div>

                        <div class="mb-0">
                            <label for="curtain_type" class="form-label small fw-bold">Kiểu May / Kiểu Rèm</label>
                            <input type="text" class="form-control form-control-sm" id="curtain_type" name="curtain_type" 
                                   value="{{ old('curtain_type', $product->curtain_type) }}">
                        </div>
                    </div>

                    <!-- Ảnh đại diện & Thư viện ảnh -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">6. Hình Ảnh Sản Phẩm</h6>

                        <div class="mb-3">
                            <label for="main_image" class="form-label small fw-bold">Ảnh Đại Diện Hiện Tại</label>
                            <div class="mb-2">
                                <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                            </div>
                            <input type="file" class="form-control form-control-sm" id="main_image" name="main_image" accept="image/*">
                        </div>

                        <div class="mb-0">
                            <label for="images" class="form-label small fw-bold">Thêm Ảnh Thực Tế Mới (Gallery)</label>
                            <input type="file" class="form-control form-control-sm" id="images" name="images[]" multiple accept="image/*">
                        </div>
                    </div>

                    <!-- Nhãn nổi bật & Trạng thái -->
                    <div class="card card-custom p-4 mb-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">7. Trạng Thái & Gắn Nhãn</h6>

                        <div class="mb-3">
                            <label for="stock_status" class="form-label small fw-bold">Tình Trạng Kho</label>
                            <select class="form-select form-select-sm" id="stock_status" name="stock_status">
                                <option value="in_stock" {{ old('stock_status', $product->stock_status) == 'in_stock' ? 'selected' : '' }}>Còn hàng (Nhận may)</option>
                                <option value="out_of_stock" {{ old('stock_status', $product->stock_status) == 'out_of_stock' ? 'selected' : '' }}>Hết hàng / Tạm dừng</option>
                                <option value="hidden" {{ old('stock_status', $product->stock_status) == 'hidden' ? 'selected' : '' }}>Ẩn khỏi website</option>
                            </select>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_hot" name="is_hot" value="1" {{ old('is_hot', $product->is_hot) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="is_hot">🔥 Sản phẩm HOT</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_trending" name="is_trending" value="1" {{ old('is_trending', $product->is_trending) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="is_trending">⚡ Sản phẩm Thịnh Hành</label>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="is_featured">⭐ Sản phẩm Nổi Bật</label>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1" {{ old('is_new', $product->is_new) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="is_new">✨ Sản phẩm Mới</label>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card card-custom p-3 text-center">
                        <button type="submit" class="btn btn-gold w-100 py-2 mb-2">
                            <i class="bi bi-check-circle me-1"></i> Cập Nhật Sản Phẩm Rèm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            Hủy bỏ
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
