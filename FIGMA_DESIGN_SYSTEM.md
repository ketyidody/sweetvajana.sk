# Figma Design System Integration Guide

This document provides comprehensive guidelines for integrating Figma designs into the SweetVajana codebase using the Model Context Protocol (MCP).

## Project Overview

**Stack**: Laravel 12 + Vue.js 3 (Composition API) + Inertia.js 2.0 + Tailwind CSS 4.0 + Vite 7

**Architecture**: Server-side rendered SPA using Inertia.js (no REST API layer)

---

## 1. Design Token Definitions

### Tailwind CSS 4.0 Theme System

Design tokens are defined using Tailwind CSS 4.0's `@theme` directive in the main CSS file.

**Location**: `resources/css/app.css`

**Current Token Structure**:
```css
@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
}
```

### Token Categories to Define

When importing designs from Figma, map design tokens to Tailwind's theme system:

#### Colors
```css
@theme {
    /* Primary Brand Colors */
    --color-primary-50: <value>;
    --color-primary-100: <value>;
    /* ... through 900 */

    /* Secondary Colors */
    --color-secondary-50: <value>;
    /* ... */

    /* Semantic Colors */
    --color-success: <value>;
    --color-error: <value>;
    --color-warning: <value>;
    --color-info: <value>;
}
```

#### Typography
```css
@theme {
    /* Font Families */
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    --font-serif: <serif-stack>;
    --font-mono: <mono-stack>;

    /* Font Sizes */
    --font-size-xs: <value>;
    --font-size-sm: <value>;
    --font-size-base: <value>;
    --font-size-lg: <value>;
    --font-size-xl: <value>;
    --font-size-2xl: <value>;
    /* ... */

    /* Line Heights */
    --line-height-tight: <value>;
    --line-height-normal: <value>;
    --line-height-relaxed: <value>;

    /* Letter Spacing */
    --letter-spacing-tight: <value>;
    --letter-spacing-normal: <value>;
    --letter-spacing-wide: <value>;
}
```

#### Spacing
```css
@theme {
    --spacing-0: 0;
    --spacing-1: 0.25rem;
    --spacing-2: 0.5rem;
    --spacing-3: 0.75rem;
    --spacing-4: 1rem;
    /* ... through 96 or custom values */
}
```

#### Border Radius
```css
@theme {
    --radius-none: 0;
    --radius-sm: 0.125rem;
    --radius-base: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
    --radius-2xl: 1rem;
    --radius-full: 9999px;
}
```

#### Shadows
```css
@theme {
    --shadow-sm: <value>;
    --shadow-base: <value>;
    --shadow-md: <value>;
    --shadow-lg: <value>;
    --shadow-xl: <value>;
    --shadow-2xl: <value>;
}
```

### Usage in Components
Tokens are accessed via Tailwind utility classes:
```vue
<template>
  <div class="text-primary-600 bg-primary-50 rounded-lg shadow-md p-4">
    <h1 class="text-2xl font-sans">Title</h1>
  </div>
</template>
```

### Token Transformation
- Figma variables → CSS custom properties in `@theme` directive
- No additional transformation layer needed
- Tailwind automatically generates utility classes from theme tokens

---

## 2. Component Library

### Structure

**Location**: `resources/js/`

```
resources/js/
├── Pages/              # Full page components (Inertia entry points)
│   └── Welcome.vue     # Example: Home page
├── Components/         # Shared/reusable components (TO BE CREATED)
│   ├── UI/            # Generic UI components
│   │   ├── Button.vue
│   │   ├── Card.vue
│   │   ├── Input.vue
│   │   └── Modal.vue
│   ├── Layout/        # Layout components
│   │   ├── Header.vue
│   │   ├── Footer.vue
│   │   └── Sidebar.vue
│   └── Product/       # Domain-specific components
│       ├── ProductCard.vue
│       ├── ProductGrid.vue
│       └── ProductDetail.vue
├── Layouts/           # Page layout wrappers (TO BE CREATED)
│   ├── AppLayout.vue
│   └── GuestLayout.vue
├── app.js             # Inertia + Vue initialization
└── bootstrap.js       # Axios and bootstrapping
```

### Component Architecture

**Framework**: Vue.js 3 Composition API

**Pattern**: Single File Components (SFC) with `<script setup>`

**Example Component Structure**:
```vue
<template>
  <div class="component-wrapper">
    <!-- Component markup with Tailwind classes -->
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props definition
const props = defineProps({
  title: String,
  items: Array
})

// Emits definition
const emit = defineEmits(['update', 'delete'])

// Component logic
const localState = ref(null)
const computedValue = computed(() => {
  // ...
})
</script>

<style scoped>
/* Component-specific styles (use sparingly, prefer Tailwind) */
</style>
```

### Component Naming Convention
- PascalCase for component files: `ProductCard.vue`, `UserProfile.vue`
- PascalCase for component imports and registration
- kebab-case for template usage (Vue auto-converts)

### Component Documentation
- Currently no Storybook configured
- Document props, emits, and slots using JSDoc comments in script section
- Consider adding Storybook for component development and documentation

---

## 3. Frameworks & Libraries

### Core Stack

**UI Framework**: Vue.js 3.5.26
- Composition API (`<script setup>`)
- No Options API
- Reactive state management with `ref()`, `reactive()`, `computed()`

**Bridge Layer**: Inertia.js 2.3.8
- Server-side routing (no REST API)
- Controllers return `Inertia::render()` responses
- Vue components receive data as props

**Styling**: Tailwind CSS 4.0.0
- Utility-first CSS framework
- New `@theme` directive for customization
- `@tailwindcss/vite` plugin for Vite integration

**Build Tool**: Vite 7.0.7
- Lightning-fast HMR (Hot Module Replacement)
- Asset optimization
- Vue SFC compilation

**HTTP Client**: Axios 1.11.0
- Configured in `resources/js/bootstrap.js`
- Automatically includes CSRF token

### Key Configuration Files

**Vite Configuration** (`vite.config.js`):
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
});
```

**Inertia Setup** (`resources/js/app.js`):
```javascript
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
```

### State Management
- No Vuex or Pinia configured
- Use Composition API `ref()` and `reactive()` for local state
- For shared state, consider:
  - Vue's `provide/inject` for component tree state
  - Composables (e.g., `useAuth()`, `useCart()`)
  - Inertia's shared data via `HandleInertiaRequests` middleware

---

## 4. Asset Management

### Storage Structure

**Public Assets**: `public/` directory
```
public/
├── favicon.ico
├── robots.txt
├── images/           # TO BE CREATED - Product images, logos, etc.
├── fonts/            # TO BE CREATED - Custom web fonts
└── videos/           # TO BE CREATED - Product videos
```

**Source Assets**: `resources/` directory
```
resources/
├── css/              # Styles processed by Vite
│   └── app.css
├── js/               # JavaScript/Vue files processed by Vite
└── views/            # Blade templates
```

### Asset Referencing

#### Static Assets (Public Directory)
Direct reference with leading slash:
```vue
<template>
  <img src="/images/logo.png" alt="Logo">
  <video src="/videos/promo.mp4"></video>
</template>
```

#### Assets Processed by Vite
Import in JavaScript/Vue files:
```vue
<script setup>
import logoImg from '@/assets/images/logo.png'
</script>

<template>
  <img :src="logoImg" alt="Logo">
</template>
```

### Asset Optimization

**Images**:
- Compress before uploading (use tools like ImageOptim, TinyPNG)
- Serve WebP format for modern browsers
- Use appropriate dimensions (avoid oversized images)
- Consider lazy loading: `<img loading="lazy">`

**Fonts**:
- Currently using Google Fonts (Instrument Sans)
- For custom fonts, add to `public/fonts/` and reference in `@theme`
- Use `font-display: swap` for better performance

**Vite Asset Optimization**:
- Automatically bundles and optimizes assets during build
- Generates versioned filenames for cache busting
- Tree-shakes unused code

### CDN Configuration
- No CDN configured by default
- For production, consider:
  - Laravel Mix's CDN features
  - Cloudflare for static asset caching
  - AWS S3 + CloudFront for media storage

### Database-Stored Assets
Images referenced in database (Product, Category models):
```php
// Single image field
'image' => 'nullable|string'  // Store path: /images/products/cake-1.jpg

// Multiple images (JSON array)
'images' => 'nullable|json'   // Store: ["img1.jpg", "img2.jpg"]
```

Access in Vue components:
```vue
<template>
  <img :src="`/images/products/${product.image}`" :alt="product.name">

  <!-- Multiple images -->
  <div v-for="img in product.images" :key="img">
    <img :src="`/images/products/${img}`">
  </div>
</template>
```

---

## 5. Icon System

### Current State
- No icon library currently configured
- No icons in `public/` directory

### Recommended Icon Libraries

#### Option 1: Heroicons (Tailwind's official icon set)
```bash
npm install @heroicons/vue
```

Usage:
```vue
<script setup>
import { ShoppingCartIcon, UserIcon } from '@heroicons/vue/24/outline'
</script>

<template>
  <ShoppingCartIcon class="w-6 h-6 text-gray-600" />
  <UserIcon class="w-5 h-5" />
</template>
```

#### Option 2: SVG Sprite System
Create `public/images/icons/sprite.svg`:
```xml
<svg xmlns="http://www.w3.org/2000/svg">
  <symbol id="icon-cart" viewBox="0 0 24 24">
    <!-- SVG path data -->
  </symbol>
</svg>
```

Usage:
```vue
<template>
  <svg class="w-6 h-6">
    <use href="/images/icons/sprite.svg#icon-cart" />
  </svg>
</template>
```

#### Option 3: Inline SVG Components
Create `resources/js/Components/Icons/CartIcon.vue`:
```vue
<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path d="..." />
  </svg>
</template>
```

### Icon Naming Convention
- Component files: `CartIcon.vue`, `UserIcon.vue` (PascalCase)
- Sprite IDs: `icon-cart`, `icon-user` (kebab-case with prefix)
- Descriptive names that indicate function, not appearance

### Icon Sizing with Tailwind
```vue
<template>
  <!-- Size utilities -->
  <IconComponent class="w-4 h-4" />    <!-- 16px -->
  <IconComponent class="w-5 h-5" />    <!-- 20px -->
  <IconComponent class="w-6 h-6" />    <!-- 24px -->
  <IconComponent class="w-8 h-8" />    <!-- 32px -->

  <!-- Color utilities -->
  <IconComponent class="text-primary-600" />
  <IconComponent class="text-gray-500" />
</template>
```

### Figma Icon Export Workflow
1. Export icons from Figma as SVG (outline/stroke format preferred)
2. Optimize SVGs with SVGO or similar tool
3. Choose implementation method:
   - Install as icon library components (Heroicons)
   - Create Vue components in `Components/Icons/`
   - Add to SVG sprite

---

## 6. Styling Approach

### CSS Methodology

**Primary**: Tailwind CSS Utility Classes
- Utility-first approach
- Compose styles directly in template
- Minimal custom CSS

**Example**:
```vue
<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-5xl font-bold text-gray-900 mb-4">
        Welcome to SweetVajana
      </h1>
      <p class="text-xl text-gray-600">
        Your favorite cake and cookie factory
      </p>
    </div>
  </div>
</template>
```

### Tailwind CSS 4.0 Features

**New @import Syntax**:
```css
@import 'tailwindcss';
```

**Content Sources** (auto-detection paths):
```css
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

**Theme Customization**:
```css
@theme {
    /* Custom design tokens */
}
```

### Global Styles

**Location**: `resources/css/app.css`

Current global styles:
```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}
```

### Component-Scoped Styles

Use `<style scoped>` sparingly for truly unique styles that can't be achieved with Tailwind:

```vue
<template>
  <div class="custom-component">
    <!-- ... -->
  </div>
</template>

<style scoped>
/* Only for complex animations, gradients, or highly specific styles */
.custom-component::before {
  content: '';
  background: linear-gradient(45deg, var(--color-primary-500), var(--color-secondary-500));
}
</style>
```

### Responsive Design Implementation

Tailwind's responsive prefixes (mobile-first):
```vue
<template>
  <!-- Mobile: stack vertically, Desktop: horizontal layout -->
  <div class="flex flex-col md:flex-row gap-4">

    <!-- Mobile: full width, Tablet: half, Desktop: third -->
    <div class="w-full md:w-1/2 lg:w-1/3">

      <!-- Responsive text sizes -->
      <h2 class="text-2xl md:text-3xl lg:text-4xl">

      <!-- Responsive spacing -->
      <div class="p-4 md:p-6 lg:p-8">

  </div>
</template>
```

**Breakpoints** (Tailwind defaults):
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px
- `2xl`: 1536px

### Dark Mode Support

Not currently configured. To add:

```css
/* app.css */
@theme {
    /* Light mode (default) */
    --color-bg: white;
    --color-text: #111;

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
        --color-bg: #111;
        --color-text: white;
    }
}
```

Or use Tailwind's `dark:` prefix with class-based dark mode.

### Reusable Style Patterns

For repeated utility combinations, create Vue composables:

```javascript
// composables/useButtonStyles.js
export function useButtonStyles() {
  const baseStyles = 'px-4 py-2 rounded-lg font-medium transition-colors'
  const primaryStyles = 'bg-primary-600 text-white hover:bg-primary-700'
  const secondaryStyles = 'bg-gray-200 text-gray-900 hover:bg-gray-300'

  return {
    baseStyles,
    primaryStyles,
    secondaryStyles
  }
}
```

---

## 7. Project Structure

### Overall Organization

```
sweetvajana.sk/
├── app/                        # Laravel application code
│   ├── Http/
│   │   ├── Controllers/        # Controllers return Inertia responses
│   │   └── Middleware/
│   │       └── HandleInertiaRequests.php  # Share data globally
│   ├── Models/                 # Eloquent models
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── Providers/
│
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Sample data
│
├── resources/
│   ├── css/
│   │   └── app.css             # Tailwind CSS entry point
│   ├── js/
│   │   ├── Components/         # Reusable Vue components (TO CREATE)
│   │   ├── Layouts/            # Layout wrappers (TO CREATE)
│   │   ├── Pages/              # Inertia page components
│   │   │   └── Welcome.vue
│   │   ├── app.js              # Inertia + Vue setup
│   │   └── bootstrap.js        # Axios config
│   └── views/
│       └── app.blade.php       # Root HTML template
│
├── routes/
│   └── web.php                 # All application routes
│
├── public/                     # Public assets
│   ├── favicon.ico
│   └── images/                 # Static images (TO CREATE)
│
├── vite.config.js              # Vite configuration
├── package.json                # npm dependencies
└── composer.json               # PHP dependencies
```

### Feature Organization Pattern

When building features, organize by domain:

```
resources/js/
├── Pages/
│   ├── Products/
│   │   ├── Index.vue           # Product listing page
│   │   ├── Show.vue            # Product detail page
│   │   └── Create.vue          # Admin: Create product
│   ├── Categories/
│   │   ├── Index.vue
│   │   └── Show.vue
│   ├── Orders/
│   │   ├── Create.vue          # Checkout page
│   │   ├── Show.vue            # Order confirmation
│   │   └── Index.vue           # Order history
│   └── Welcome.vue             # Home page
│
├── Components/
│   ├── Product/                # Product-specific components
│   │   ├── ProductCard.vue
│   │   ├── ProductGrid.vue
│   │   └── ProductFilter.vue
│   ├── Cart/                   # Shopping cart components
│   │   ├── CartItem.vue
│   │   └── CartSummary.vue
│   └── UI/                     # Generic UI components
│       ├── Button.vue
│       ├── Card.vue
│       └── Modal.vue
```

### Routes to Components Mapping

Routes defined in `routes/web.php` map to page components:

```php
// routes/web.php
Route::get('/', [HomeController::class, 'index']);
// → Inertia::render('Welcome') → resources/js/Pages/Welcome.vue

Route::get('/products', [ProductController::class, 'index']);
// → Inertia::render('Products/Index') → resources/js/Pages/Products/Index.vue

Route::get('/products/{product}', [ProductController::class, 'show']);
// → Inertia::render('Products/Show') → resources/js/Pages/Products/Show.vue
```

### Data Flow Pattern

```
User Request
    ↓
Laravel Route (web.php)
    ↓
Controller Method
    ↓
Eloquent Model Query
    ↓
Inertia::render('PageComponent', $data)
    ↓
Vue Page Component (receives data as props)
    ↓
Child Components (via props/slots)
```

---

## 8. Figma to Code Workflow

### Step 1: Extract Design Tokens
1. Export Figma variables (colors, typography, spacing)
2. Map to Tailwind theme in `resources/css/app.css`
3. Use `@theme` directive for custom tokens

### Step 2: Component Creation
1. Identify reusable components in Figma
2. Create Vue SFC in appropriate directory:
   - `Pages/` for full pages
   - `Components/UI/` for generic UI elements
   - `Components/{Domain}/` for domain-specific components
3. Use `<script setup>` Composition API
4. Apply Tailwind classes for styling

### Step 3: Asset Handling
1. Export images/icons from Figma
2. Optimize (compress, resize)
3. Place in `public/images/` or `public/icons/`
4. Reference with absolute paths `/images/...`

### Step 4: Route Integration
1. Create controller action that returns `Inertia::render()`
2. Define route in `routes/web.php`
3. Pass data from controller to Vue component as props

### Step 5: Responsive Implementation
1. Use Tailwind responsive prefixes (`md:`, `lg:`, etc.)
2. Test on multiple breakpoints
3. Ensure mobile-first approach

### Step 6: Testing
1. Run `npm run dev` for hot reload during development
2. Test Inertia navigation between pages
3. Verify data passing from controller to component
4. Check responsive behavior

---

## 9. Best Practices for Figma Integration

### Do's
- ✅ Extract design tokens to Tailwind theme first
- ✅ Create reusable components for repeated UI patterns
- ✅ Use Tailwind utility classes for styling
- ✅ Optimize images before adding to codebase
- ✅ Follow Vue 3 Composition API patterns
- ✅ Use TypeScript types for props (optional but recommended)
- ✅ Test responsive behavior across breakpoints

### Don'ts
- ❌ Don't create REST API endpoints (use Inertia)
- ❌ Don't use Vue Options API (use Composition API)
- ❌ Don't write custom CSS unless absolutely necessary
- ❌ Don't forget to eager load relationships (prevent N+1 queries)
- ❌ Don't add components to `Pages/` that aren't full pages
- ❌ Don't skip image optimization

### Component Checklist
- [ ] Component created in correct directory
- [ ] Uses `<script setup>` syntax
- [ ] Props defined with `defineProps()`
- [ ] Emits defined with `defineEmits()` if needed
- [ ] Styled with Tailwind utilities
- [ ] Responsive design implemented
- [ ] Accessible (semantic HTML, ARIA labels)
- [ ] Performance optimized (lazy loading, etc.)

---

## 10. Common Patterns

### Page Component with Data
```vue
<!-- resources/js/Pages/Products/Index.vue -->
<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Products</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>
  </div>
</template>

<script setup>
import ProductCard from '@/Components/Product/ProductCard.vue'

const props = defineProps({
  products: Array
})
</script>
```

```php
// app/Http/Controllers/ProductController.php
public function index()
{
    return Inertia::render('Products/Index', [
        'products' => Product::with('category')->get()
    ]);
}
```

### Reusable UI Component
```vue
<!-- resources/js/Components/UI/Button.vue -->
<template>
  <button
    :type="type"
    :class="buttonClasses"
    @click="handleClick"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: Boolean
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
  const base = 'px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50'
  const variants = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300',
    outline: 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50'
  }
  return `${base} ${variants[props.variant]}`
})

const handleClick = (event) => {
  if (!props.disabled) {
    emit('click', event)
  }
}
</script>
```

### Form with Validation
```vue
<!-- resources/js/Pages/Products/Create.vue -->
<template>
  <form @submit.prevent="submit" class="space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Product Name</label>
      <input
        v-model="form.name"
        type="text"
        class="w-full border rounded-lg px-3 py-2"
        :class="{ 'border-red-500': form.errors.name }"
      >
      <span v-if="form.errors.name" class="text-red-500 text-sm">
        {{ form.errors.name }}
      </span>
    </div>

    <button
      type="submit"
      :disabled="form.processing"
      class="bg-blue-600 text-white px-4 py-2 rounded-lg"
    >
      Create Product
    </button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  price: '',
  description: ''
})

const submit = () => {
  form.post('/products', {
    onSuccess: () => {
      // Handle success
    }
  })
}
</script>
```

---

## Quick Reference

### File Paths
- Design tokens: `resources/css/app.css` (`@theme` directive)
- Vue components: `resources/js/Components/`
- Page components: `resources/js/Pages/`
- Static assets: `public/images/`, `public/fonts/`
- Routes: `routes/web.php`
- Controllers: `app/Http/Controllers/`

### Commands
- Dev server: `php artisan serve` + `npm run dev`
- Build assets: `npm run build`
- Create controller: `php artisan make:controller ControllerName`
- Run migrations: `php artisan migrate`

### Key Principles
1. No REST API needed (Inertia handles it)
2. Utility-first styling with Tailwind
3. Composition API for all Vue components
4. Mobile-first responsive design
5. Optimize assets before deployment
