# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SweetVajana is an e-commerce platform for a cake and cookie factory. The application allows customers to browse products, view categories, and place orders for cakes and cookies.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 with Composition API
- **Bridge**: Inertia.js 2.0 (connects Laravel and Vue without building an API)
- **CSS**: Tailwind CSS 4.0
- **Build Tool**: Vite 7
- **Database**: SQLite (default), configurable to MySQL/PostgreSQL

## Development Commands

### Initial Setup
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file (if not exists)
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database with sample data (when seeders are created)
php artisan db:seed
```

### Development Server
```bash
# Start Laravel development server (runs on http://localhost:8000)
php artisan serve

# In a separate terminal, start Vite dev server for hot module replacement
npm run dev

# Or run both concurrently (if configured)
concurrently "php artisan serve" "npm run dev"
```

### Building for Production
```bash
# Build frontend assets for production
npm run build

# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Operations
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database and run all migrations
php artisan migrate:fresh

# Reset database and seed
php artisan migrate:fresh --seed

# Create a new migration
php artisan make:migration create_table_name

# Create model with migration
php artisan make:model ModelName -m
```

### Testing
```bash
# Run PHPUnit tests
php artisan test

# Run specific test file
php artisan test --filter TestClassName

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Fix all files
./vendor/bin/pint

# Check formatting without fixing
./vendor/bin/pint --test
```

### Artisan Commands
```bash
# Create a controller
php artisan make:controller ControllerName

# Create a resource controller (with CRUD methods)
php artisan make:controller ControllerName --resource

# Create a model with migration, factory, and seeder
php artisan make:model ModelName -mfs

# Create a request validation class
php artisan make:request RequestName

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Architecture & Code Structure

### Inertia.js Architecture

This project uses **Inertia.js** to connect Laravel and Vue.js without building a traditional API. Understanding this architecture is crucial:

- **Server-side routing**: All routes are defined in `routes/web.php` using Laravel's routing system
- **No REST API needed**: Inertia handles the communication between Laravel controllers and Vue components
- **Page components**: Vue components in `resources/js/Pages/` represent full pages, not fragments
- **Data passing**: Controllers return Inertia responses with `Inertia::render('ComponentName', ['data' => $data])`
- **Client-side navigation**: Inertia provides seamless SPA-like navigation without full page reloads

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/        # Laravel controllers that return Inertia responses
│   └── Middleware/
│       └── HandleInertiaRequests.php  # Share data across all Inertia requests
├── Models/                 # Eloquent models (Category, Product, Order, OrderItem)
└── Providers/

resources/
├── js/
│   ├── Pages/              # Vue page components (each represents a route)
│   │   └── Welcome.vue     # Example home page
│   ├── app.js              # Inertia.js and Vue initialization
│   └── bootstrap.js        # Axios and other bootstrapping
├── css/
│   └── app.css             # Tailwind CSS imports
└── views/
    └── app.blade.php       # Root Blade template for Inertia

routes/
└── web.php                 # All application routes (uses Inertia::render())

database/
├── migrations/             # Database schema migrations
└── seeders/                # Database seeders for sample data
```

### Database Schema

The application has four core tables:

**categories**
- Stores product categories (e.g., "Cakes", "Cookies")
- Fields: name, slug, description, image, is_active

**products**
- Stores individual products
- Fields: category_id (FK), name, slug, description, price, stock, image, images (JSON), is_active, is_featured
- Relationship: belongs to Category

**orders**
- Stores customer orders
- Fields: user_id (nullable FK), order_number, customer details, addresses, pricing (subtotal, tax, shipping, total), status, payment_method, payment_status
- Relationships: belongs to User, has many OrderItems

**order_items**
- Stores individual items within an order
- Fields: order_id (FK), product_id (FK), product_name, price, quantity, subtotal
- Relationships: belongs to Order and Product

### Models and Relationships

**Category Model** (`app/Models/Category.php`)
- `hasMany(Product::class)` - A category has many products

**Product Model** (`app/Models/Product.php`)
- `belongsTo(Category::class)` - Each product belongs to a category
- Casts: price (decimal), images (array), is_active (boolean), is_featured (boolean)

**Order Model** (`app/Models/Order.php`)
- `belongsTo(User::class)` - Order can belong to a user (optional)
- `hasMany(OrderItem::class)` - Order has many items
- Casts: subtotal, tax, shipping, total (all decimal)

**OrderItem Model** (`app/Models/OrderItem.php`)
- `belongsTo(Order::class)` - Item belongs to an order
- `belongsTo(Product::class)` - Item references a product
- Casts: price, subtotal (decimal)

## Working with Inertia.js

### Creating a New Page

1. **Create a Vue component** in `resources/js/Pages/`:
```vue
<!-- resources/js/Pages/Products/Index.vue -->
<template>
  <div>
    <h1>Products</h1>
    <div v-for="product in products" :key="product.id">
      {{ product.name }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  products: Array
})
</script>
```

2. **Create a route and controller action**:
```php
// routes/web.php
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);

// app/Http/Controllers/ProductController.php
public function index()
{
    return Inertia::render('Products/Index', [
        'products' => Product::with('category')->get()
    ]);
}
```

### Sharing Data Globally

To share data across all Inertia pages (e.g., auth user, flash messages), edit `app/Http/Middleware/HandleInertiaRequests.php`:

```php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'message' => fn () => $request->session()->get('message')
        ],
    ]);
}
```

### Making Inertia Requests

Use the Inertia Link component or router for navigation:

```vue
<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <!-- Using Link component -->
  <Link href="/products">View Products</Link>

  <!-- Using router for programmatic navigation -->
  <button @click="$inertia.visit('/products')">Go to Products</button>
</template>
```

## Key Development Patterns

### Route Definitions
- All routes are defined in `routes/web.php`
- Use `Inertia::render('PageComponent', $data)` instead of `view()` or JSON responses
- Route parameters work the same as standard Laravel

### Controller Responses
- Return `Inertia::render()` with the component name and data
- Component paths use dot notation: `'Products/Show'` maps to `resources/js/Pages/Products/Show.vue`
- Data passed becomes props in the Vue component

### Vue Component Props
- Use `defineProps()` composition API to receive data from controllers
- Props are automatically validated and reactive

### Forms and Validation
- Use Inertia's form helper for easy form handling with validation
- Server-side validation in Laravel Request classes or controller methods
- Errors automatically shared back to Vue components

### Asset Management
- Place images and static assets in `public/` directory
- Use Vite for bundling JS/CSS from `resources/`
- Reference public assets with `/` prefix in templates

## Environment Configuration

Key `.env` variables:
- `APP_NAME`: Application name (currently "SweetVajana")
- `APP_URL`: Application URL
- `DB_CONNECTION`: Database driver (sqlite, mysql, pgsql)
- `DB_DATABASE`: Database path/name

## Common Tasks

### Adding a New Product Category
1. Create category via database seeder or manual insertion
2. Ensure slug is unique and URL-friendly

### Creating Product Listings
1. Query products with eager loading: `Product::with('category')->get()`
2. Pass to Inertia component
3. Render in Vue template

### Handling Orders
1. Generate unique order_number (e.g., using UUID or custom format)
2. Calculate subtotal, tax, shipping, and total
3. Create Order with OrderItems in a database transaction
4. Store product snapshot data (name, price) in OrderItem for historical accuracy

## Performance Considerations

- Use eager loading to prevent N+1 queries: `Product::with('category')`
- Cache frequently accessed data (categories, featured products)
- Implement pagination for large product lists
- Optimize images before uploading (compress, resize)

## Database Conventions

- Use migrations for all schema changes (never modify database directly)
- Foreign keys should use `constrained()` for automatic relationship handling
- Use `onDelete('cascade')` or `onDelete('set null')` appropriately
- Boolean fields default to `false` unless specified
- Price fields use `decimal(10, 2)` for precision

## MCP Servers

### Figma MCP Integration Rules

These rules define how to translate Figma inputs into code for this project and must be followed for every Figma-driven change.

#### Required Flow
1. Run get_design_context first to fetch the structured representation for the exact node(s)
2. If the response is too large or truncated, run get_metadata to get the high-level node map and then re-fetch only the required node(s) with get_design_context
3. Run get_screenshot for a visual reference of the node variant being implemented
4. Only after you have both get_design_context and get_screenshot, download any assets needed and start implementation
5. Translate the output (usually React + Tailwind) into Vue.js 3 Composition API with Inertia.js for this project
6. Reuse the project's color tokens, components, and typography wherever possible
7. Validate against Figma for 1:1 look and behavior before marking complete

#### Implementation Rules
- Treat the Figma MCP output (React + Tailwind) as a representation of design and behavior, not as final code style
- Always adapt React components to Vue.js 3 Composition API (`<script setup>`)
- Replace Tailwind utility classes with the project's design system tokens when applicable
- Reuse existing components (buttons, inputs, typography) instead of duplicating functionality
- Use the project's color system (defined in `resources/css/app.css`), typography scale, and spacing tokens consistently
- Respect Inertia.js patterns: no REST API, use `Inertia::render()` from controllers
- Strive for 1:1 visual parity with the Figma design
- Validate the final UI against the Figma screenshot for both look and behavior

#### Asset Handling
- The Figma MCP server provides an assets endpoint which can serve image and SVG assets
- IMPORTANT: If the Figma MCP server returns a localhost source for an image or an SVG, use that image or SVG source directly
- IMPORTANT: DO NOT import/add new icon packages, all the assets should be in the Figma payload or use lucide-vue-next for icons
- IMPORTANT: Do NOT use or create placeholders if a localhost source is provided
