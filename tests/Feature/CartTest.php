<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;
    protected Color $color;

    protected function setUp(): void
    {
        parent::setUp();

        $this->color = Color::create([
            'name' => 'Kem Sữa',
            'hex_code' => '#FDFBF7',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Rèm Vải Gấm Bỉ Cao Cấp',
            'sku' => 'RV-GAM-01',
            'slug' => 'rem-vai-gam-bi-cao-cap',
            'unit_price' => 350000,
            'sale_price' => 300000,
            'min_width' => 1.0,
            'max_width' => 6.0,
            'min_height' => 1.5,
            'max_height' => 4.5,
            'stock_status' => 'in_stock',
        ]);
        $this->product->colors()->attach($this->color->id, ['stock_quantity' => 50]);
    }

    public function test_guest_can_view_cart_page(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee('Giỏ hàng của bạn đang trống');
    }

    public function test_user_can_add_custom_curtain_to_cart(): void
    {
        $response = $this->post(route('cart.store'), [
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
            'width' => 2.5,
            'height' => 2.8,
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
            'width' => 2.5,
            'height' => 2.8,
            'area' => 7.00,
            'quantity' => 2,
        ]);
    }

    public function test_cart_service_calculates_correct_subtotal_and_area(): void
    {
        $cartService = app(CartService::class);
        $cartService->addToCart([
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
            'width' => 2.0,
            'height' => 3.0,
            'quantity' => 1,
        ]);

        // Area = 2.0 * 3.0 = 6.0 m²
        $this->assertEquals(6.0, $cartService->getTotalArea());
        // Sale price = 300.000 đ * 6.0 m² = 1.800.000 đ
        $this->assertEquals(1800000.0, $cartService->getSubtotal());
    }

    public function test_user_can_update_quantity_and_remove_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cartService = app(CartService::class);
        $item = $cartService->addToCart([
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
            'width' => 2.0,
            'height' => 2.0,
            'quantity' => 1,
        ]);

        $response = $this->patch(route('cart.update', $item->id), [
            'quantity' => 3,
        ]);
        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);

        $deleteResponse = $this->delete(route('cart.destroy', $item->id));
        $deleteResponse->assertRedirect(route('cart.index'));
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
