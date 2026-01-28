<template>
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="handleCartClick" />

    <main class="flex-1">
      <Hero />
      <ProductGrid :products="products" @product-click="handleProductClick" />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Header from '@/Components/Layout/Header.vue'
import Hero from '@/Components/Layout/Hero.vue'
import Footer from '@/Components/Layout/Footer.vue'
import ProductGrid from '@/Components/Product/ProductGrid.vue'

const props = defineProps({
  products: {
    type: Array,
    default: () => []
  }
})

// Cart state (simplified for now - will be enhanced later)
const cart = ref([])

const cartItemsCount = computed(() => {
  return cart.value.reduce((total, item) => total + item.quantity, 0)
})

const handleCartClick = () => {
  console.log('Cart clicked')
  // TODO: Implement cart drawer
}

const handleProductClick = (product) => {
  console.log('Product clicked:', product)
  // TODO: Implement product modal
}
</script>
