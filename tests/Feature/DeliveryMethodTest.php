<?php

namespace Tests\Feature;

use App\Delivery\DeliveryDriver;
use App\Delivery\DeliveryManager;
use App\Delivery\StorePickup;
use App\Models\Category;
use App\Models\DeliveryMethod;
use App\Models\Language;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\GoPayService;
use GoPay\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeliveryMethodTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private DeliveryMethod $pickup;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.recaptcha.secret_key' => null]);

        $response = new Response;
        $response->statusCode = 500;
        $response->json = [];
        $this->mock(GoPayService::class)->shouldReceive('createPayment')->andReturn($response);

        $category = Category::create(['name' => 'Cakes', 'slug' => 'cakes', 'is_active' => true]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Cheesecake',
            'slug' => 'cheesecake',
            'price' => 20,
            'is_active' => true,
            'is_orderable_online' => true,
        ]);

        $this->pickup = DeliveryMethod::where('key', 'store_pickup')->sole();
    }

    private function useCourier(array $attributes = []): DeliveryMethod
    {
        $this->app->instance(DeliveryManager::class, new DeliveryManager([StorePickup::class, FakeCourier::class]));

        return DeliveryMethod::create([
            'key' => 'courier',
            'name' => 'Courier',
            'price' => 4.5,
            'is_active' => true,
            'position' => 2,
            'settings' => ['api_username' => 'shop', 'api_password' => 'secret'],
            ...$attributes,
        ]);
    }

    private function checkout(array $data = [])
    {
        if (empty(session('cart'))) {
            $this->post('/cart', ['product_id' => $this->product->id, 'quantity' => 1]);
        }

        return $this->post('/checkout', array_merge([
            'customer_name' => 'Jana',
            'customer_email' => 'jana@example.com',
            'customer_phone' => '0900000000',
            'payment_method' => 'gopay',
            'recaptcha_token' => 'x',
        ], $data));
    }

    private function admin()
    {
        return $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    public function test_checkout_page_lists_active_delivery_methods(): void
    {
        $this->pickup->update(['settings' => ['address' => 'Hlavná 1, Bratislava']]);
        $this->useCourier(['is_active' => false]);
        $this->post('/cart', ['product_id' => $this->product->id, 'quantity' => 1]);

        $this->get('/checkout')->assertInertia(fn ($page) => $page
            ->has('deliveryMethods', 1)
            ->where('deliveryMethods.0.key', 'store_pickup')
            ->where('deliveryMethods.0.name', 'Osobný odber')
            ->where('deliveryMethods.0.requires_address', false)
            ->where('deliveryMethods.0.price', '0.00')
            ->where('deliveryMethods.0.details', 'Hlavná 1, Bratislava'));
    }

    public function test_store_pickup_order_needs_no_shipping_address(): void
    {
        $this->checkout(['delivery_method' => 'store_pickup', 'shipping_address' => 'ignored'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $order = Order::sole();
        $this->assertSame('store_pickup', $order->delivery_method);
        $this->assertNull($order->shipping_address);
        $this->assertSame('0.00', $order->shipping);
        $this->assertSame('20.00', $order->total);
    }

    public function test_delivery_method_must_be_known_and_enabled(): void
    {
        $this->checkout()->assertSessionHasErrors('delivery_method');
        $this->checkout(['delivery_method' => 'teleport'])->assertSessionHasErrors('delivery_method');

        $this->pickup->update(['is_active' => false]);
        $this->checkout(['delivery_method' => 'store_pickup'])->assertSessionHasErrors('delivery_method');

        $this->assertSame(0, Order::count());
    }

    public function test_unconfigured_method_is_not_offered(): void
    {
        $this->useCourier(['settings' => ['api_username' => 'shop']]);

        $this->checkout(['delivery_method' => 'courier', 'shipping_address' => 'Hlavná 1'])
            ->assertSessionHasErrors('delivery_method');
    }

    public function test_address_based_method_requires_address_and_adds_configured_price(): void
    {
        $this->useCourier();

        $this->checkout(['delivery_method' => 'courier'])->assertSessionHasErrors('shipping_address');

        $this->checkout(['delivery_method' => 'courier', 'shipping_address' => 'Hlavná 1'])
            ->assertSessionHasNoErrors();

        $order = Order::sole();
        $this->assertSame('Hlavná 1', $order->shipping_address);
        $this->assertSame('Hlavná 1', $order->billing_address);
        $this->assertSame('4.50', $order->shipping);
        $this->assertSame('24.50', $order->total);
    }

    public function test_admin_can_edit_name_translation_and_price(): void
    {
        Language::create(['code' => 'sk', 'name' => 'Slovak', 'native_name' => 'Slovenčina', 'is_active' => true, 'is_default' => true]);
        Language::create(['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_active' => true, 'is_default' => false]);
        cache()->forget('languages.default');

        $this->admin()->put("/admin/delivery-methods/{$this->pickup->id}", [
            'name' => 'Vyzdvihnutie v predajni',
            'description' => '',
            'price' => '1.50',
            'is_active' => true,
            'translations' => ['en' => ['name' => 'Pick up in store', 'description' => '']],
            'settings' => ['address' => 'Hlavná 1'],
        ])->assertSessionHasNoErrors()->assertRedirect('/admin/delivery-methods');

        $this->pickup->refresh();
        $this->assertSame('1.50', $this->pickup->price);
        $this->assertSame('Hlavná 1', $this->pickup->settings['address']);
        $this->assertSame('Vyzdvihnutie v predajni', $this->pickup->driver()->name('sk'));
        $this->assertSame('Pick up in store', $this->pickup->load('translations')->driver()->name('en'));
    }

    public function test_admin_cannot_enable_method_without_required_settings(): void
    {
        $courier = $this->useCourier(['is_active' => false, 'settings' => null]);

        $this->admin()->put("/admin/delivery-methods/{$courier->id}", [
            'name' => 'Courier',
            'price' => 4.5,
            'is_active' => true,
            'settings' => ['api_username' => 'shop', 'api_password' => ''],
        ])->assertSessionHasErrors('settings.api_password');

        $this->patch("/admin/delivery-methods/{$courier->id}/toggle")->assertSessionHas('error');
        $this->assertFalse($courier->fresh()->is_active);
    }

    public function test_saved_password_is_kept_when_left_empty_and_stored_encrypted(): void
    {
        $courier = $this->useCourier();

        $this->admin()->get("/admin/delivery-methods/{$courier->id}/edit")
            ->assertInertia(fn ($page) => $page
                ->where('settingsFields.1.key', 'api_password')
                ->where('settingsFields.1.value', '')
                ->where('settingsFields.1.has_value', true));

        $this->put("/admin/delivery-methods/{$courier->id}", [
            'name' => 'Courier',
            'price' => 4.5,
            'is_active' => true,
            'settings' => ['api_username' => 'shop2', 'api_password' => ''],
        ])->assertSessionHasNoErrors();

        $this->assertSame(['api_username' => 'shop2', 'api_password' => 'secret'], $courier->fresh()->settings);
        $this->assertStringNotContainsString('secret', DB::table('delivery_methods')->where('id', $courier->id)->value('settings'));
    }

    public function test_admin_can_toggle_and_reorder(): void
    {
        $courier = $this->useCourier();

        $this->admin()->patch("/admin/delivery-methods/{$this->pickup->id}/toggle");
        $this->assertFalse($this->pickup->fresh()->is_active);

        $this->post('/admin/delivery-methods/reorder', ['ids' => [$courier->id, $this->pickup->id]]);
        $this->assertSame(1, $courier->fresh()->position);
        $this->assertSame(2, $this->pickup->fresh()->position);
    }

    public function test_admin_index_registers_new_drivers_as_disabled(): void
    {
        $this->app->instance(DeliveryManager::class, new DeliveryManager([StorePickup::class, FakeCourier::class]));

        $this->admin()->get('/admin/delivery-methods')->assertInertia(fn ($page) => $page
            ->has('methods', 2)
            ->where('methods.1.key', 'courier')
            ->where('methods.1.is_active', false)
            ->where('methods.1.is_configured', false));
    }
}

class FakeCourier extends DeliveryDriver
{
    public static function key(): string
    {
        return 'courier';
    }

    public static function defaultName(): string
    {
        return 'Courier';
    }

    public static function requiresAddress(): bool
    {
        return true;
    }

    public static function settingsFields(): array
    {
        return [
            ['key' => 'api_username', 'label' => 'API username', 'type' => 'text', 'required' => true],
            ['key' => 'api_password', 'label' => 'API password', 'type' => 'password', 'required' => true],
        ];
    }
}
