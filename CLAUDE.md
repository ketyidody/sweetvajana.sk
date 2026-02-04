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
- **Database**: SQLite (local default), MySQL (Docker)
- **Icons**: lucide-vue-next

## Development Commands

### Quick Start (Local)
```bash
composer setup    # Full setup: install deps, generate key, migrate, build
composer dev      # Start all dev servers concurrently (Laravel, Vite, queue, logs)
```

### Quick Start (Docker)
```bash
docker compose up -d              # Production-like: nginx + php-fpm + MySQL
docker compose --profile dev up   # Development: hot reload + MySQL
docker compose exec app php artisan migrate  # Run migrations
docker compose down -v            # Stop and remove volumes
```

### Individual Commands
```bash
# PHP dependencies
composer install

# JavaScript dependencies
npm install

# Development servers (run separately if needed)
php artisan serve     # Laravel server at http://localhost:8000
npm run dev           # Vite HMR server

# Build for production
npm run build
```

### Database
```bash
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Reset and seed database
php artisan make:model ModelName -m  # Create model with migration
```

### Testing & Code Quality
```bash
composer test              # Run tests (clears config first)
php artisan test --filter TestClassName  # Run specific test
./vendor/bin/pint          # Format code with Laravel Pint
./vendor/bin/pint --test   # Check formatting without fixing
```

### Artisan Generators
```bash
php artisan make:controller ControllerName --resource
php artisan make:model ModelName -mfs  # With migration, factory, seeder
php artisan make:request RequestName
```

## Architecture

### Inertia.js Pattern

This project uses Inertia.js - no REST API is needed:

1. Routes are defined in `routes/web.php`
2. Controllers return `Inertia::render('PageComponent', $data)`
3. Vue components in `resources/js/Pages/` receive data as props
4. Use `<Link>` component or `$inertia.visit()` for navigation

```php
// Controller
return Inertia::render('Products/Index', [
    'products' => Product::with('category')->get()
]);
```

```vue
<!-- resources/js/Pages/Products/Index.vue -->
<script setup>
defineProps({ products: Array })
</script>
```

### Directory Structure

```
app/
├── Http/Controllers/           # Return Inertia responses
├── Http/Middleware/HandleInertiaRequests.php  # Share global data
└── Models/                     # Category, Product, Order, OrderItem

resources/
├── js/
│   ├── Pages/                  # Inertia page components
│   ├── Components/             # Reusable Vue components
│   │   ├── UI/                 # Generic UI (Button, etc.)
│   │   ├── Layout/             # Header, Footer, Hero
│   │   └── Product/            # ProductCard, ProductGrid
│   └── lib/utils.js            # Utility functions
├── css/app.css                 # Tailwind + design tokens
└── views/app.blade.php         # Root Blade template

routes/web.php                  # All routes (Inertia renders)
```

### Database Schema

**categories**: name, slug, description, image, is_active

**products**: category_id (FK), name, slug, description, price, stock, image, images (JSON), is_active, is_featured

**orders**: user_id (nullable), order_number, customer details, addresses, subtotal, tax, shipping, total, status, payment_method, payment_status

**order_items**: order_id, product_id, product_name, price, quantity, subtotal

### Model Relationships
- Category `hasMany` Product
- Product `belongsTo` Category
- Order `hasMany` OrderItem, `belongsTo` User (optional)
- OrderItem `belongsTo` Order, `belongsTo` Product

## Design System

Design tokens are defined in `resources/css/app.css` using CSS custom properties and Tailwind's `@theme` directive.

### Color Tokens
Use semantic color names in Tailwind classes:
- `bg-background`, `text-foreground` - Base colors
- `bg-primary`, `text-primary-foreground` - Primary actions
- `bg-secondary`, `text-secondary-foreground` - Secondary elements
- `bg-muted`, `text-muted-foreground` - Subtle/disabled states
- `bg-accent`, `text-accent-foreground` - Highlights
- `bg-destructive`, `text-destructive-foreground` - Errors/delete actions
- `bg-card`, `text-card-foreground` - Card components

### Border Radius
- `rounded-sm`, `rounded-md`, `rounded-lg`, `rounded-xl` - All derive from `--radius`

### Dark Mode
Dark mode is configured via `.dark` class. Use `dark:` prefix for dark mode variants.

## Key Patterns

### Vue Components
- Always use `<script setup>` Composition API
- Use `defineProps()` for props, `defineEmits()` for events
- Import icons from `lucide-vue-next`

### Data Loading
- Use eager loading: `Product::with('category')->get()`
- Share global data via `HandleInertiaRequests.php`

### Forms
```vue
<script setup>
import { useForm } from '@inertiajs/vue3'
const form = useForm({ name: '', price: '' })
const submit = () => form.post('/products')
</script>
```

### Navigation
```vue
<script setup>
import { Link } from '@inertiajs/vue3'
</script>
<template>
  <Link href="/products">Products</Link>
</template>
```

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
