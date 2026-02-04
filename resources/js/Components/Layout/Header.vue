<template>
  <header class="sticky top-0 z-50 bg-white border-b border-border">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 md:h-20">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <h1 class="text-xl md:text-2xl tracking-tight">Sweet Vajana</h1>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <a href="#home" class="text-foreground hover:text-foreground/70 transition-colors">
            Home
          </a>
          <Link href="/products" class="text-foreground hover:text-foreground/70 transition-colors">
            Products
          </Link>
          <a href="#about" class="text-foreground hover:text-foreground/70 transition-colors">
            About
          </a>
          <a href="#contact" class="text-foreground hover:text-foreground/70 transition-colors">
            Contact
          </a>
        </nav>

        <!-- Cart Button -->
        <div class="flex items-center gap-4">
          <Button
            variant="outline"
            size="icon"
            class="relative"
            @click="$emit('cart-click')"
          >
            <ShoppingCartIcon class="h-5 w-5" />
            <span
              v-if="cartItemsCount > 0"
              class="absolute -top-2 -right-2 bg-primary text-primary-foreground rounded-full w-6 h-6 flex items-center justify-center text-xs"
            >
              {{ cartItemsCount }}
            </span>
          </Button>

          <!-- Mobile Menu Button -->
          <button
            class="md:hidden p-2"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
          >
            <XIcon v-if="isMobileMenuOpen" class="h-6 w-6" />
            <MenuIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <nav v-if="isMobileMenuOpen" class="md:hidden py-4 space-y-4 border-t border-border">
        <a
          href="#home"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          Home
        </a>
        <Link
          href="/products"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          Products
        </Link>
        <a
          href="#about"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          About
        </a>
        <a
          href="#contact"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          Contact
        </a>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ShoppingCart as ShoppingCartIcon, Menu as MenuIcon, X as XIcon } from 'lucide-vue-next'
import Button from '@/Components/UI/Button.vue'

defineProps({
  cartItemsCount: {
    type: Number,
    default: 0
  }
})

defineEmits(['cart-click'])

const isMobileMenuOpen = ref(false)
</script>
