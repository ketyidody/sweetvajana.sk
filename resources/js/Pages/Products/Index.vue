<template>
  <Head title="Products" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit('/cart')" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl md:text-4xl mb-8">Products</h1>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <aside class="w-full lg:w-64 flex-shrink-0">
          <div class="space-y-6">
            <!-- Category Filter -->
            <div>
              <h3 class="font-medium mb-3">Category</h3>
              <ul class="space-y-2">
                <li>
                  <button
                    class="text-sm transition-colors"
                    :class="!filters.category ? 'text-primary font-medium' : 'text-muted-foreground hover:text-foreground'"
                    @click="applyFilter('category', null)"
                  >
                    All
                  </button>
                </li>
                <li v-for="category in categories" :key="category.slug">
                  <button
                    class="text-sm transition-colors"
                    :class="filters.category === category.slug ? 'text-primary font-medium' : 'text-muted-foreground hover:text-foreground'"
                    @click="applyFilter('category', category.slug)"
                  >
                    {{ category.name }}
                  </button>
                </li>
              </ul>
            </div>

            <!-- Price Filter -->
            <div>
              <h3 class="font-medium mb-3">Price Range</h3>
              <div class="flex items-center gap-2">
                <input
                  v-model="priceMin"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="Min"
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background"
                  @keyup.enter="applyPriceFilter"
                />
                <span class="text-muted-foreground">–</span>
                <input
                  v-model="priceMax"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="Max"
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background"
                  @keyup.enter="applyPriceFilter"
                />
              </div>
              <button
                class="mt-2 text-sm text-primary hover:text-primary/80 transition-colors"
                @click="applyPriceFilter"
              >
                Apply
              </button>
            </div>

            <!-- Clear Filters -->
            <button
              v-if="filters.category || filters.min_price || filters.max_price"
              class="text-sm text-destructive hover:text-destructive/80 transition-colors"
              @click="clearFilters"
            >
              Clear all filters
            </button>
          </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
          <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            <ProductCard
              v-for="product in products.data"
              :key="product.id"
              :product="product"
            />
          </div>
          <div v-else class="text-center py-16">
            <p class="text-muted-foreground text-lg">No products found matching your filters.</p>
          </div>

          <!-- Pagination -->
          <nav v-if="products.last_page > 1" class="mt-8 flex justify-center items-center gap-1">
            <!-- First page -->
            <Link
              v-if="products.current_page > 1"
              :href="pageUrl(1)"
              class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border transition-colors hover:bg-muted"
              preserve-scroll
            >
              <ChevronsLeftIcon class="w-4 h-4" />
            </Link>
            <span v-else class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border text-muted-foreground opacity-50 cursor-default">
              <ChevronsLeftIcon class="w-4 h-4" />
            </span>

            <!-- Previous page -->
            <Link
              v-if="products.prev_page_url"
              :href="products.prev_page_url"
              class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border transition-colors hover:bg-muted"
              preserve-scroll
            >
              <ChevronLeftIcon class="w-4 h-4" />
            </Link>
            <span v-else class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border text-muted-foreground opacity-50 cursor-default">
              <ChevronLeftIcon class="w-4 h-4" />
            </span>

            <!-- Page numbers -->
            <template v-for="page in pageNumbers" :key="page">
              <span v-if="page === '...'" class="px-2.5 py-2 text-sm text-muted-foreground">...</span>
              <span
                v-else-if="page === products.current_page"
                class="inline-flex items-center justify-center px-3 py-2 text-sm rounded-md border border-primary bg-primary text-primary-foreground min-w-[38px]"
              >
                {{ page }}
              </span>
              <Link
                v-else
                :href="pageUrl(page)"
                class="inline-flex items-center justify-center px-3 py-2 text-sm rounded-md border border-border transition-colors hover:bg-muted min-w-[38px]"
                preserve-scroll
              >
                {{ page }}
              </Link>
            </template>

            <!-- Next page -->
            <Link
              v-if="products.next_page_url"
              :href="products.next_page_url"
              class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border transition-colors hover:bg-muted"
              preserve-scroll
            >
              <ChevronRightIcon class="w-4 h-4" />
            </Link>
            <span v-else class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border text-muted-foreground opacity-50 cursor-default">
              <ChevronRightIcon class="w-4 h-4" />
            </span>

            <!-- Last page -->
            <Link
              v-if="products.current_page < products.last_page"
              :href="pageUrl(products.last_page)"
              class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border transition-colors hover:bg-muted"
              preserve-scroll
            >
              <ChevronsRightIcon class="w-4 h-4" />
            </Link>
            <span v-else class="inline-flex items-center justify-center px-2.5 py-2 text-sm rounded-md border border-border text-muted-foreground opacity-50 cursor-default">
              <ChevronsRightIcon class="w-4 h-4" />
            </span>
          </nav>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon, ChevronsLeft as ChevronsLeftIcon, ChevronsRight as ChevronsRightIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)

const priceMin = ref(props.filters.min_price || '')
const priceMax = ref(props.filters.max_price || '')

const pageNumbers = computed(() => {
  const current = props.products.current_page
  const last = props.products.last_page
  const pages = []
  const delta = 1

  const rangeStart = Math.max(2, current - delta)
  const rangeEnd = Math.min(last - 1, current + delta)

  pages.push(1)

  if (rangeStart > 2) {
    pages.push('...')
  }

  for (let i = rangeStart; i <= rangeEnd; i++) {
    pages.push(i)
  }

  if (rangeEnd < last - 1) {
    pages.push('...')
  }

  if (last > 1) {
    pages.push(last)
  }

  return pages
})

function pageUrl(page) {
  const params = { ...props.filters }
  if (page > 1) {
    params.page = page
  } else {
    delete params.page
  }
  const query = new URLSearchParams(
    Object.entries(params).filter(([, v]) => v != null)
  ).toString()
  return '/products' + (query ? '?' + query : '')
}

function applyFilter(key, value) {
  const params = { ...props.filters }
  if (value) {
    params[key] = value
  } else {
    delete params[key]
  }
  router.get('/products', params, { preserveState: true })
}

function applyPriceFilter() {
  const params = { ...props.filters }
  if (priceMin.value) {
    params.min_price = priceMin.value
  } else {
    delete params.min_price
  }
  if (priceMax.value) {
    params.max_price = priceMax.value
  } else {
    delete params.max_price
  }
  router.get('/products', params, { preserveState: true })
}

function clearFilters() {
  priceMin.value = ''
  priceMax.value = ''
  router.get('/products', {}, { preserveState: true })
}
</script>
