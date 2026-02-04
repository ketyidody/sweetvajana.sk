<template>
  <section id="products" class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
      <h2 class="text-3xl md:text-4xl mb-4">{{ title }}</h2>
      <p class="text-muted-foreground text-lg">
        {{ subtitle }}
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        @click="$emit('product-click', product)"
      />
    </div>
  </section>
</template>

<script setup>
import ProductCard from './ProductCard.vue'

defineProps({
  products: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: 'Our Delicious Selection'
  },
  subtitle: {
    type: String,
    default: 'Handcrafted with love, baked fresh daily'
  },
})

defineEmits(['product-click'])
</script>
