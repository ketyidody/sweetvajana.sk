<template>
  <Head :title="siteSettings.site_name || 'Home'" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit(localizedUrl('/cart'))" />

    <main class="flex-1 bg-pink-500/10">
      <Hero />
      <ProductGrid
        v-if="featuredProducts.length"
        :products="featuredProducts"
        :title="t('product.featured')"
        :subtitle="t('product.featured_subtitle')"
      />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Header from '@/Components/Layout/Header.vue'
import Hero from '@/Components/Layout/Hero.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ProductGrid from '@/Components/Product/ProductGrid.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

defineProps({
  featuredProducts: {
    type: Array,
    default: () => []
  },
})

const { t } = useTranslation()
const { localizedUrl } = useLocale()
const cartItemsCount = computed(() => usePage().props.cartItemsCount)
const siteSettings = computed(() => usePage().props.site_settings || {})
</script>
