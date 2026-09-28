<?php

namespace App\Services;

use App\Models\Product;

class PriceCalculator
{
    /**
     * Tính diện tích rèm (m²) từ chiều rộng và chiều cao (mét)
     * Công thức: Diện tích = Chiều rộng × Chiều cao
     */
    public static function calculateArea(float $width, float $height): float
    {
        return round($width * $height, 4);
    }

    /**
     * Tính tổng tiền của một bộ rèm theo diện tích và đơn giá
     * Công thức: Thành tiền = Diện tích (m²) × Đơn giá/m² × Số lượng
     */
    public static function calculateItemTotal(float $area, float $unitPrice, int $quantity = 1): float
    {
        return round($area * $unitPrice * $quantity, 2);
    }

    /**
     * Kiểm tra tính hợp lệ của kích thước rèm so với giới hạn xưởng
     */
    public static function validateDimensions(Product $product, float $width, float $height): array
    {
        $errors = [];

        if ($width <= 0) {
            $errors[] = 'Chiều rộng phải lớn hơn 0 mét.';
        } elseif ($product->min_width && $width < $product->min_width) {
            $errors[] = "Chiều rộng tối thiểu xưởng nhận may là {$product->min_width}m.";
        } elseif ($product->max_width && $width > $product->max_width) {
            $errors[] = "Chiều rộng tối đa xưởng nhận may là {$product->max_width}m.";
        }

        if ($height <= 0) {
            $errors[] = 'Chiều cao phải lớn hơn 0 mét.';
        } elseif ($product->min_height && $height < $product->min_height) {
            $errors[] = "Chiều cao tối thiểu xưởng nhận may là {$product->min_height}m.";
        } elseif ($product->max_height && $height > $product->max_height) {
            $errors[] = "Chiều cao tối đa xưởng nhận may là {$product->max_height}m.";
        }

        return $errors;
    }

    /**
     * Định dạng tiền tệ VNĐ
     */
    public static function formatPrice(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}
