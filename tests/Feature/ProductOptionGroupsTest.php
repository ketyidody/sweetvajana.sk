<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\OptionGroup;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\GoPayService;
use GoPay\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductOptionGroupsTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private OptionGroup $flavours;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Pralines', 'slug' => 'pralines', 'is_active' => true]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Pralines box of 12',
            'slug' => 'pralines-12',
            'price' => 18,
            'is_active' => true,
            'is_orderable_online' => true,
        ]);

        $this->flavours = OptionGroup::create(['name' => 'Flavours']);
        foreach (['Hazelnut', 'Raspberry', 'Pistachio', 'Coffee', 'Caramel'] as $name) {
            $this->flavours->values()->create(['name' => $name]);
        }
    }

    private function assignFlavours(int $min, int $max): void
    {
        $this->product->optionGroups()->attach($this->flavours->id, [
            'min_selections' => $min,
            'max_selections' => $max,
        ]);
    }

    private function valueIds(int $count): array
    {
        return $this->flavours->values()->limit($count)->pluck('id')->all();
    }

    public function test_admin_can_assign_option_group_to_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put("/admin/products/{$this->product->id}", [
            'category_id' => $this->product->category_id,
            'name' => $this->product->name,
            'price' => 18,
            'is_orderable_online' => true,
            'option_groups' => [
                ['id' => $this->flavours->id, 'min_selections' => 1, 'max_selections' => 4],
            ],
        ])->assertSessionHasNoErrors();

        $group = $this->product->optionGroups()->first();
        $this->assertSame(1, $group->pivot->min_selections);
        $this->assertSame(4, $group->pivot->max_selections);
    }

    public function test_admin_cannot_set_max_below_min(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put("/admin/products/{$this->product->id}", [
            'category_id' => $this->product->category_id,
            'name' => $this->product->name,
            'price' => 18,
            'is_orderable_online' => true,
            'option_groups' => [
                ['id' => $this->flavours->id, 'min_selections' => 3, 'max_selections' => 2],
            ],
        ])->assertSessionHasErrors('option_groups.0.max_selections');
    }

    public function test_adding_to_cart_requires_option_selection(): void
    {
        $this->assignFlavours(1, 4);

        $this->post('/cart', ['product_id' => $this->product->id, 'quantity' => 1])
            ->assertSessionHasErrors("options.{$this->flavours->id}");

        $this->assertEmpty(session('cart', []));
    }

    public function test_adding_to_cart_rejects_too_many_options(): void
    {
        $this->assignFlavours(1, 4);

        $this->post('/cart', [
            'product_id' => $this->product->id,
            'options' => [$this->flavours->id => $this->valueIds(5)],
        ])->assertSessionHasErrors("options.{$this->flavours->id}");
    }

    public function test_adding_to_cart_rejects_values_from_other_groups(): void
    {
        $this->assignFlavours(1, 4);
        $foreign = OptionGroup::create(['name' => 'Other'])->values()->create(['name' => 'X']);

        $this->post('/cart', [
            'product_id' => $this->product->id,
            'options' => [$this->flavours->id => [$foreign->id]],
        ])->assertSessionHasErrors("options.{$this->flavours->id}");
    }

    public function test_different_option_picks_create_separate_cart_lines(): void
    {
        $this->assignFlavours(1, 4);
        [$a, $b, $c] = $this->valueIds(3);

        $this->post('/cart', ['product_id' => $this->product->id, 'options' => [$this->flavours->id => [$b, $a]]]);
        $this->post('/cart', ['product_id' => $this->product->id, 'options' => [$this->flavours->id => [$a, $b]]]);
        $this->post('/cart', ['product_id' => $this->product->id, 'options' => [$this->flavours->id => [$c]]]);

        $cart = session('cart');
        $this->assertCount(2, $cart);
        $this->assertSame(2, $cart["{$this->product->id}-{$a}.{$b}"]['quantity']);
        $this->assertSame(1, $cart["{$this->product->id}-{$c}"]['quantity']);

        $this->get('/cart')->assertInertia(fn ($page) => $page
            ->component('Cart/Index')
            ->has('items', 2)
            ->where('items.0.options.0.group', 'Flavours')
            ->where('items.0.options.0.values', ['Hazelnut', 'Raspberry'])
            ->where('subtotal', '54.00')
        );
    }

    public function test_cart_line_quantity_can_be_updated_and_removed_by_key(): void
    {
        $this->assignFlavours(1, 1);
        [$a] = $this->valueIds(1);
        $key = "{$this->product->id}-{$a}";

        $this->post('/cart', ['product_id' => $this->product->id, 'options' => [$this->flavours->id => [$a]]]);

        $this->patch("/cart/{$key}", ['quantity' => 3]);
        $this->assertSame(3, session("cart.{$key}.quantity"));

        $this->delete("/cart/{$key}");
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_stores_selected_options_on_order_item(): void
    {
        config(['services.recaptcha.secret_key' => null]);

        $response = new Response;
        $response->statusCode = 500;
        $response->json = [];
        $this->mock(GoPayService::class)->shouldReceive('createPayment')->andReturn($response);

        $this->assignFlavours(1, 4);
        [$a, $b] = $this->valueIds(2);

        $this->post('/cart', ['product_id' => $this->product->id, 'quantity' => 2, 'options' => [$this->flavours->id => [$a, $b]]]);

        $this->post('/checkout', [
            'customer_name' => 'Jana',
            'customer_email' => 'jana@example.com',
            'customer_phone' => '0900000000',
            'shipping_address' => 'Hlavna 1',
            'payment_method' => 'gopay',
            'recaptcha_token' => 'x',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $item = Order::sole()->items()->sole();
        $this->assertSame([['group' => 'Flavours', 'values' => ['Hazelnut', 'Raspberry']]], $item->options);
        $this->assertSame(2, $item->quantity);
        $this->assertSame('36.00', $item->subtotal);
    }

    public function test_products_without_option_groups_still_work_with_legacy_cart(): void
    {
        $this->withSession(['cart' => [$this->product->id => ['quantity' => 2]]])
            ->get('/cart')
            ->assertInertia(fn ($page) => $page->has('items', 1)->where('subtotal', '36.00'));
    }

    public function test_cart_drops_lines_whose_options_are_no_longer_valid(): void
    {
        $this->assignFlavours(1, 4);
        [$a] = $this->valueIds(1);

        $this->post('/cart', ['product_id' => $this->product->id, 'options' => [$this->flavours->id => [$a]]]);
        $this->product->optionGroups()->updateExistingPivot($this->flavours->id, ['min_selections' => 2]);

        $this->get('/cart')->assertInertia(fn ($page) => $page->has('items', 0));
    }
}
