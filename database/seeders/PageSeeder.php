<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'content' => "Welcome to Sweet Vajana, where every bite tells a story of passion, tradition, and the finest ingredients.\n\nOur bakery was founded with a simple mission: to bring joy through handcrafted cakes, cookies, and sweets made fresh daily. We believe that the best treats are made with love, patience, and a commitment to quality that never wavers.\n\nEvery recipe we use has been perfected over the years, blending time-honoured techniques with creative flavours that surprise and delight. From classic butter cookies to elaborate celebration cakes, each creation is a labour of love.\n\nWe source our ingredients from trusted local suppliers, ensuring that every product meets our exacting standards. No shortcuts, no compromises — just pure, delicious goodness.\n\nThank you for choosing Sweet Vajana. We look forward to being part of your celebrations and everyday moments alike.",
                'is_active' => true,
            ]
        );
    }
}
