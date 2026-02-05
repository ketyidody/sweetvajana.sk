<template>
  <Head :title="t('cart.title')" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => {}" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl md:text-4xl mb-8">{{ t('cart.title') }}</h1>

      <div v-if="items.length" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-4">
          <div
            v-for="item in items"
            :key="item.product_id"
            class="flex gap-4 p-4 bg-card rounded-lg border border-border"
          >
            <Link :href="localizedUrl('/products/' + item.slug)" class="flex-shrink-0">
              <img
                :src="item.image"
                :alt="item.name"
                class="w-20 h-20 object-cover rounded-md bg-muted"
              />
            </Link>
            <div class="flex-1 min-w-0">
              <Link :href="localizedUrl('/products/' + item.slug)" class="font-medium hover:text-primary transition-colors">
                {{ item.name }}
              </Link>
              <p class="text-primary mt-1">${{ item.price }}</p>
              <div class="flex items-center gap-3 mt-2">
                <div class="flex items-center border border-border rounded-md">
                  <button
                    class="px-3 py-1 text-sm hover:bg-muted transition-colors"
                    @click="updateQuantity(item.product_id, item.quantity - 1)"
                  >
                    <MinusIcon class="w-3 h-3" />
                  </button>
                  <span class="px-3 py-1 text-sm min-w-[2.5rem] text-center border-x border-border">
                    {{ item.quantity }}
                  </span>
                  <button
                    class="px-3 py-1 text-sm hover:bg-muted transition-colors"
                    :disabled="item.quantity >= item.stock"
                    :class="item.quantity >= item.stock ? 'opacity-50 cursor-not-allowed' : ''"
                    @click="updateQuantity(item.product_id, item.quantity + 1)"
                  >
                    <PlusIcon class="w-3 h-3" />
                  </button>
                </div>
                <button
                  class="text-sm text-destructive hover:text-destructive/80 transition-colors"
                  @click="removeItem(item.product_id)"
                >
                  <Trash2Icon class="w-4 h-4" />
                </button>
              </div>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="font-medium">${{ item.subtotal }}</p>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-card rounded-lg border border-border p-6 sticky top-24">
            <h2 class="text-lg font-medium mb-4">{{ t('cart.order_summary') }}</h2>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ t('cart.subtotal') }}</span>
                <span>${{ subtotal }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ t('cart.shipping') }}</span>
                <span>{{ t('cart.free') }}</span>
              </div>
              <div class="border-t border-border pt-2 mt-2 flex justify-between font-medium text-base">
                <span>{{ t('cart.total') }}</span>
                <span>${{ total }}</span>
              </div>
            </div>
            <Link
              :href="localizedUrl('/checkout')"
              class="mt-6 w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors text-sm font-medium"
            >
              {{ t('cart.proceed_to_checkout') }}
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <ShoppingCartIcon class="w-16 h-16 mx-auto text-muted-foreground/50 mb-4" />
        <p class="text-muted-foreground text-lg mb-4">{{ t('cart.empty') }}</p>
        <Link
          :href="localizedUrl('/products')"
          class="inline-flex items-center justify-center px-6 py-2.5 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors text-sm font-medium"
        >
          {{ t('cart.browse_products') }}
        </Link>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { ShoppingCart as ShoppingCartIcon, Minus as MinusIcon, Plus as PlusIcon, Trash2 as Trash2Icon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()

defineProps({
  items: Array,
  subtotal: String,
  total: String,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)

function updateQuantity(productId, quantity) {
  if (quantity <= 0) {
    removeItem(productId)
    return
  }
  router.patch(localizedUrl(`/cart/${productId}`), { quantity }, { preserveScroll: true })
}

function removeItem(productId) {
  router.delete(localizedUrl(`/cart/${productId}`), { preserveScroll: true })
}
</script>
