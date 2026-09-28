<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'short_description',
        'description',
        'unit_price',
        'sale_price',
        'min_width',
        'max_width',
        'min_height',
        'max_height',
        'main_image',
        'material',
        'style',
        'curtain_type',
        'stock_status',
        'is_hot',
        'is_trending',
        'is_featured',
        'is_new',
        'view_count',
        'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'min_width' => 'decimal:2',
            'max_width' => 'decimal:2',
            'min_height' => 'decimal:2',
            'max_height' => 'decimal:2',
            'is_hot' => 'boolean',
            'is_trending' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'view_count' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'REM-' . strtoupper(Str::random(6));
            }
        });
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_color')
            ->withPivot('stock_quantity')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isWishlistedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : 5.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getStarRatingHtmlAttribute(): string
    {
        $rating = $this->average_rating;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } elseif ($halfStar && $i == $fullStars + 1) {
                $html .= '<i class="bi bi-star-half text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $html;
    }

    // ==========================================
    // ACCESSORS & HELPERS (TÍNH GIÁ & HIỂN THỊ)
    // ==========================================

    /**
     * Đơn giá áp dụng thực tế (nếu có khuyến mãi thì lấy sale_price)
     */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->unit_price);
    }

    /**
     * Tính % giảm giá nếu có khuyến mãi
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->sale_price && $this->unit_price > 0 && $this->sale_price < $this->unit_price) {
            return (int) round((($this->unit_price - $this->sale_price) / $this->unit_price) * 100);
        }
        return null;
    }

    /**
     * Định dạng tiền tệ VNĐ cho giá gốc / m²
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 0, ',', '.') . ' đ/m²';
    }

    /**
     * Định dạng tiền tệ VNĐ cho giá sale / m²
     */
    public function getFormattedSalePriceAttribute(): ?string
    {
        return $this->sale_price ? number_format($this->sale_price, 0, ',', '.') . ' đ/m²' : null;
    }

    /**
     * Định dạng tiền tệ VNĐ cho đơn giá thực tế / m²
     */
    public function getFormattedEffectivePriceAttribute(): string
    {
        return number_format($this->effective_price, 0, ',', '.') . ' đ/m²';
    }

    /**
     * Link ảnh chính (nếu không có thì trả về ảnh placeholder)
     */
    public function getMainImageUrlAttribute(): string
    {
        if ($this->main_image) {
            if (Str::startsWith($this->main_image, ['http://', 'https://'])) {
                return $this->main_image;
            }
            if (Str::startsWith($this->main_image, 'images/')) {
                return asset($this->main_image);
            }
            if (file_exists(public_path('images/products/' . $this->main_image))) {
                return asset('images/products/' . $this->main_image);
            }
            return asset('storage/' . $this->main_image);
        }
        return 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800&auto=format&fit=crop';
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('stock_status', '!=', 'hidden');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeHot($query)
    {
        return $query->where('is_hot', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Bộ lọc tìm kiếm sản phẩm đa tiêu chí
     */
    public function scopeFilter($query, array $filters)
    {
        // 1. Tìm theo từ khóa (Tên hoặc SKU)
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('sku', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // 2. Lọc theo Danh mục (slug)
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // 3. Lọc theo Chất liệu
        if (!empty($filters['material'])) {
            $query->where('material', $filters['material']);
        }

        // 4. Lọc theo Kiểu rèm
        if (!empty($filters['curtain_type'])) {
            $query->where('curtain_type', $filters['curtain_type']);
        }

        // 5. Lọc theo Phong cách
        if (!empty($filters['style'])) {
            $query->where('style', $filters['style']);
        }

        // 6. Lọc theo khoảng giá / m²
        if (!empty($filters['min_price'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('sale_price', '>=', $filters['min_price'])
                  ->orWhere(function ($sq) use ($filters) {
                      $sq->whereNull('sale_price')->where('unit_price', '>=', $filters['min_price']);
                  });
            });
        }
        if (!empty($filters['max_price'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('sale_price', '<=', $filters['max_price'])
                  ->orWhere(function ($sq) use ($filters) {
                      $sq->whereNull('sale_price')->where('unit_price', '<=', $filters['max_price']);
                  });
            });
        }

        // 7. Sắp xếp
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderByRaw('COALESCE(sale_price, unit_price) ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw('COALESCE(sale_price, unit_price) DESC');
                    break;
                case 'popular':
                    $query->orderBy('sold_count', 'desc');
                    break;
                case 'views':
                    $query->orderBy('view_count', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        return $query;
    }
}
