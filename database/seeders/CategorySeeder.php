<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Root categories
        $cakes = Category::create([
            'name' => 'Cakes',
            'slug' => 'cakes',
            'description' => 'Delicious handcrafted cakes for all occasions',
            'is_active' => true,
            'position' => 0,
        ]);

        Category::create([
            'name' => 'Cupcakes',
            'slug' => 'cupcakes',
            'description' => 'Individual portion cupcakes with gourmet frosting',
            'is_active' => true,
            'position' => 1,
        ]);

        Category::create([
            'name' => 'Pastries',
            'slug' => 'pastries',
            'description' => 'French-inspired pastries and sweet treats',
            'is_active' => true,
            'position' => 2,
        ]);

        Category::create([
            'name' => 'Desserts',
            'slug' => 'desserts',
            'description' => 'Classic desserts made with premium ingredients',
            'is_active' => true,
            'position' => 3,
        ]);

        // Subcategories under Cakes
        Category::create([
            'name' => 'Red Cakes',
            'slug' => 'red-cakes',
            'description' => 'Stunning red and burgundy toned cakes',
            'is_active' => true,
            'parent_id' => $cakes->id,
            'position' => 0,
        ]);

        Category::create([
            'name' => 'Blue Cakes',
            'slug' => 'blue-cakes',
            'description' => 'Beautiful blue and navy toned cakes',
            'is_active' => true,
            'parent_id' => $cakes->id,
            'position' => 1,
        ]);

        Category::create([
            'name' => 'Green Cakes',
            'slug' => 'green-cakes',
            'description' => 'Fresh green and emerald toned cakes',
            'is_active' => true,
            'parent_id' => $cakes->id,
            'position' => 2,
        ]);

        Category::create([
            'name' => 'Pink Cakes',
            'slug' => 'pink-cakes',
            'description' => 'Lovely pink and rose toned cakes',
            'is_active' => true,
            'parent_id' => $cakes->id,
            'position' => 3,
        ]);
    }
}
