<template>
  <Head :title="t('product.products')" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit(localizedUrl('/cart'))" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl md:text-4xl mb-6">{{ t('product.products') }}</h1>

      <!-- Category Menu -->
      <div ref="menuRef" class="flex flex-wrap items-center gap-1 mb-8">
        <button
          class="px-4 py-2 text-sm rounded-md transition-colors"
          :class="!filters.category
            ? 'bg-primary text-primary-foreground'
            : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
          @click="applyFilter(null)"
        >
          {{ t('product.all') }}
        </button>
        <div
          v-for="cat in categoryTree"
          :key="cat.slug"
          class="relative"
          @mouseenter="cat.children.length && (openMenu = cat.slug)"
          @mouseleave="openMenu = null"
        >
          <button
            class="inline-flex items-center gap-1 px-4 py-2 text-sm rounded-md transition-colors"
            :class="isCategoryActive(cat)
              ? 'bg-primary text-primary-foreground'
              : 'text-muted-foreground hover:text-foreground hover:bg-muted'"
            @click="onCategoryClick(cat)"
          >
            {{ cat.name }}
            <ChevronDownIcon v-if="cat.children.length" class="w-3.5 h-3.5" />
          </button>
          <!-- Dropdown -->
          <div
            v-if="cat.children.length && openMenu === cat.slug"
            class="absolute left-0 top-full z-10 min-w-[180px] pt-1"
          >
            <div class="rounded-md border border-border bg-card py-1 shadow-lg">
              <button
                v-for="child in cat.children"
                :key="child.slug"
                class="block w-full text-left px-4 py-2 text-sm transition-colors"
                :class="filters.category === child.slug
                  ? 'bg-primary/10 text-primary font-medium'
                  : 'text-card-foreground hover:bg-muted'"
                @click="applyFilter(child.slug); openMenu = null"
              >
                {{ child.name }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in products.data"
          :key="product.id"
          :product="product"
        />
      </div>
      <div v-else class="text-center py-16">
        <p class="text-muted-foreground text-lg">{{ t('product.no_products_found') }}</p>
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
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ChevronDown as ChevronDownIcon, ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon, ChevronsLeft as ChevronsLeftIcon, ChevronsRight as ChevronsRightIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ProductCard from '@/Components/Product/ProductCard.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)
const openMenu = ref(null)
const menuRef = ref(null)

function onCategoryClick(cat) {
  if (cat.children.length && openMenu.value !== cat.slug) {
    openMenu.value = cat.slug
  } else {
    openMenu.value = null
    applyFilter(cat.slug)
  }
}

function onClickOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    openMenu.value = null
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onUnmounted(() => document.removeEventListener('click', onClickOutside))

// Build tree from flat depth-ordered list
const categoryTree = computed(() => {
  const roots = []
  let currentParent = null

  for (const cat of props.categories) {
    if (cat.depth === 0) {
      currentParent = { ...cat, children: [] }
      roots.push(currentParent)
    } else if (currentParent) {
      currentParent.children.push(cat)
    }
  }

  return roots
})

function isCategoryActive(cat) {
  if (props.filters.category === cat.slug) return true
  return cat.children.some(c => props.filters.category === c.slug)
}

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
  return localizedUrl('/products') + (query ? '?' + query : '')
}

function applyFilter(slug) {
  const params = {}
  if (slug) {
    params.category = slug
  }
  router.get(localizedUrl('/products'), params, { preserveState: true })
}
</script>
