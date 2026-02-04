<template>
  <Head :title="product.name" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit('/cart')" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back link -->
      <Link
        href="/products"
        class="inline-flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors mb-6"
      >
        <ArrowLeftIcon class="w-4 h-4" />
        Back to Products
      </Link>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Image Gallery -->
        <div>
          <div class="aspect-square overflow-hidden rounded-lg bg-muted border border-border">
            <img
              :src="selectedImage"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>
          <div v-if="product.images.length > 1" class="grid grid-cols-4 gap-3 mt-3">
            <button
              v-for="(image, index) in product.images"
              :key="index"
              class="aspect-square overflow-hidden rounded-md bg-muted border-2 transition-colors"
              :class="selectedImage === image ? 'border-primary' : 'border-border hover:border-primary/50'"
              @click="selectedImage = image"
            >
              <img
                :src="image"
                :alt="`${product.name} - Image ${index + 1}`"
                class="w-full h-full object-cover"
              />
            </button>
          </div>
        </div>

        <!-- Product Info -->
        <div>
          <span class="inline-block px-3 py-1 bg-muted text-muted-foreground rounded-full text-xs mb-4">
            {{ product.category }}
          </span>
          <h1 class="text-3xl md:text-4xl mb-4">{{ product.name }}</h1>
          <p class="text-2xl text-primary mb-6">${{ product.price }}</p>
          <p class="text-muted-foreground leading-relaxed mb-6">{{ product.description }}</p>

          <div class="flex items-center gap-2 text-sm mb-6">
            <span
              class="inline-flex items-center gap-1.5"
              :class="product.stock > 0 ? 'text-green-600' : 'text-destructive'"
            >
              <CheckCircleIcon v-if="product.stock > 0" class="w-4 h-4" />
              <XCircleIcon v-else class="w-4 h-4" />
              {{ product.stock > 0 ? `In stock (${product.stock})` : 'Out of stock' }}
            </span>
          </div>

          <!-- Add to Cart -->
          <div v-if="product.stock > 0" class="flex items-center gap-3">
            <div class="flex items-center border border-border rounded-md">
              <button
                class="px-3 py-2 hover:bg-muted transition-colors"
                :disabled="quantity <= 1"
                :class="quantity <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                @click="quantity > 1 && quantity--"
              >
                <MinusIcon class="w-4 h-4" />
              </button>
              <input
                v-model.number="quantity"
                type="number"
                min="1"
                :max="product.stock"
                class="w-14 text-center py-2 text-sm border-x border-border bg-background focus:outline-none"
                @change="clampQuantity"
              />
              <button
                class="px-3 py-2 hover:bg-muted transition-colors"
                :disabled="quantity >= product.stock"
                :class="quantity >= product.stock ? 'opacity-50 cursor-not-allowed' : ''"
                @click="quantity < product.stock && quantity++"
              >
                <PlusIcon class="w-4 h-4" />
              </button>
            </div>
            <button
              class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-colors"
              @click="addToCart"
            >
              <ShoppingCartIcon class="w-4 h-4" />
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ArrowLeft as ArrowLeftIcon, CheckCircle as CheckCircleIcon, XCircle as XCircleIcon, Minus as MinusIcon, Plus as PlusIcon, ShoppingCart as ShoppingCartIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'

const props = defineProps({
  product: Object,
})

const selectedImage = ref(props.product.image)
const quantity = ref(1)
const cartItemsCount = computed(() => usePage().props.cartItemsCount)

function clampQuantity() {
  if (quantity.value < 1) quantity.value = 1
  if (quantity.value > props.product.stock) quantity.value = props.product.stock
}

function addToCart() {
  router.post('/cart', { product_id: props.product.id, quantity: quantity.value }, { preserveScroll: true })
}
</script>
