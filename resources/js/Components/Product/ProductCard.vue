<template>
  <div
    class="group bg-card rounded-lg overflow-hidden border border-border hover:shadow-lg transition-all duration-300 cursor-pointer"
    @click="router.visit(localizedUrl('/products/' + product.slug))"
  >
    <div class="aspect-square overflow-hidden bg-muted">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
    </div>
    <div class="p-4">
      <div class="flex items-start justify-between gap-2 mb-2">
        <h3 class="line-clamp-1">{{ product.name }}</h3>
        <span class="text-primary flex-shrink-0">${{ product.price }}</span>
      </div>
      <p class="text-muted-foreground line-clamp-2 text-sm">
        {{ product.description }}
      </p>
      <div class="mt-3 flex items-center justify-between">
        <span class="inline-block px-3 py-1 bg-muted text-muted-foreground rounded-full text-xs">
          {{ product.category }}
        </span>
        <button
          v-if="product.stock > 0"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-primary-foreground rounded-md text-xs font-medium hover:bg-primary/90 transition-colors"
          @click.stop="addToCart"
        >
          <ShoppingCartIcon class="w-3.5 h-3.5" />
          {{ t('common.add_to_cart') }}
        </button>
        <span v-else class="text-xs text-muted-foreground">{{ t('product.out_of_stock') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ShoppingCart as ShoppingCartIcon } from 'lucide-vue-next'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

function addToCart() {
  router.post(localizedUrl('/cart'), { product_id: props.product.id, quantity: 1 }, { preserveScroll: true })
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
