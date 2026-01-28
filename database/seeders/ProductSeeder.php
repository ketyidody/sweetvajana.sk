<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Cakes',
                'slug' => 'cakes',
                'description' => 'Delicious handcrafted cakes for all occasions',
                'is_active' => true,
            ],
            [
                'name' => 'Cupcakes',
                'slug' => 'cupcakes',
                'description' => 'Individual portion cupcakes with gourmet frosting',
                'is_active' => true,
            ],
            [
                'name' => 'Pastries',
                'slug' => 'pastries',
                'description' => 'French-inspired pastries and sweet treats',
                'is_active' => true,
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'description' => 'Classic desserts made with premium ingredients',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Get categories for relationships
        $cakes = Category::where('slug', 'cakes')->first();
        $cupcakes = Category::where('slug', 'cupcakes')->first();
        $pastries = Category::where('slug', 'pastries')->first();
        $desserts = Category::where('slug', 'desserts')->first();

        // Create products
        $products = [
            [
                'category_id' => $cakes->id,
                'name' => 'Chocolate Dream Cake',
                'slug' => 'chocolate-dream-cake',
                'description' => 'Rich, moist chocolate cake with velvety chocolate ganache and chocolate curls. A chocolate lover\'s paradise in every bite.',
                'price' => 45.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaG9jb2xhdGUlMjBjYWtlfGVufDF8fHx8MTc2ODA0NzM5M3ww&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $cakes->id,
                'name' => 'Strawberry Delight',
                'slug' => 'strawberry-delight',
                'description' => 'Light and fluffy vanilla cake layered with fresh strawberries and whipped cream. Perfect for any celebration.',
                'price' => 42.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1602663491496-73f07481dbea?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzdHJhd2JlcnJ5JTIwY2FrZXxlbnwxfHx8fDE3NjgwMjgxNDl8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $cakes->id,
                'name' => 'Red Velvet Classic',
                'slug' => 'red-velvet-classic',
                'description' => 'Traditional red velvet cake with cream cheese frosting. Perfectly moist with a hint of cocoa and a stunning red color.',
                'price' => 48.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1586788680434-30d324b2d46f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxyZWQlMjB2ZWx2ZXQlMjBjYWtlfGVufDF8fHx8MTc2ODA2MjcwMHww&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $cupcakes->id,
                'name' => 'Gourmet Cupcake Box',
                'slug' => 'gourmet-cupcake-box',
                'description' => 'Assortment of 12 gourmet cupcakes in various flavors including vanilla, chocolate, and red velvet with artisan frosting.',
                'price' => 36.00,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1572978577832-287ca6539e9b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjdXBjYWtlcyUyMGRpc3BsYXl8ZW58MXx8fHwxNzY4MDYyNjQ1fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $pastries->id,
                'name' => 'French Macarons',
                'slug' => 'french-macarons',
                'description' => 'Box of 12 delicate French macarons in assorted flavors: raspberry, pistachio, vanilla, and chocolate.',
                'price' => 28.00,
                'stock' => 20,
                'image' => 'https://images.unsplash.com/photo-1612198790172-e176281228ae?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYWNhcm9ucyUyMGNvbG9yZnVsfGVufDF8fHx8MTc2Nzk4MTUzOXww&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $pastries->id,
                'name' => 'Lemon Tart',
                'slug' => 'lemon-tart',
                'description' => 'Tangy lemon curd in a buttery shortcrust pastry shell, topped with torched meringue. A refreshing citrus treat.',
                'price' => 32.00,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1543508185-225c92847541?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsZW1vbiUyMHRhcnR8ZW58MXx8fHwxNzY4MDAzMjI2fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $desserts->id,
                'name' => 'Classic Tiramisu',
                'slug' => 'classic-tiramisu',
                'description' => 'Italian dessert with layers of coffee-soaked ladyfingers and mascarpone cream, dusted with cocoa powder.',
                'price' => 38.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx0aXJhbWlzdXxlbnwxfHx8fDE3NjgwNDMyMzh8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $cakes->id,
                'name' => 'New York Cheesecake',
                'slug' => 'new-york-cheesecake',
                'description' => 'Creamy, dense cheesecake on a graham cracker crust. Served with your choice of berry compote or caramel sauce.',
                'price' => 40.00,
                'stock' => 10,
                'image' => 'https://images.unsplash.com/photo-1524351199678-941a58a3df50?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaGVlc2VjYWtlfGVufDF8fHx8MTc2ODAyOTQ0NXww&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $pastries->id,
                'name' => 'Chocolate Éclairs',
                'slug' => 'chocolate-eclairs',
                'description' => 'Box of 6 classic French éclairs filled with vanilla cream and topped with rich chocolate glaze.',
                'price' => 24.00,
                'stock' => 15,
                'image' => 'https://images.unsplash.com/photo-1552598827-ea6f80fcbcfc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlY2xhaXIlMjBwYXN0cnl8ZW58MXx8fHwxNzY4MDYyNzAyfDA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $pastries->id,
                'name' => 'Artisan Pastry Box',
                'slug' => 'artisan-pastry-box',
                'description' => 'Selection of 8 artisan pastries including croissants, pain au chocolat, and danishes. Perfect for sharing.',
                'price' => 30.00,
                'stock' => 12,
                'image' => 'https://images.unsplash.com/photo-1737700089128-cbbb2dc71631?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwYXN0cnklMjBzd2VldHN8ZW58MXx8fHwxNzY4MDYyNjQ0fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
