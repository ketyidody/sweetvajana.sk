<template>
  <header class="sticky top-0 z-50 bg-pink-500/10 border-b border-border">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 md:h-20">
        <!-- Logo -->
        <Link :href="localizedUrl('/')" class="flex-shrink-0">
          <img v-if="settings.logo" :src="settings.logo" :alt="settings.site_name || 'Sweet Vajana'" class="h-12 md:h-15 w-auto object-contain" />
          <span v-else class="text-xl md:text-2xl tracking-tight">{{ settings.site_name || 'Sweet Vajana' }}</span>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <Link :href="localizedUrl('/')" class="text-foreground hover:text-foreground/70 transition-colors">
            {{ t('nav.home') }}
          </Link>
          <Link :href="localizedUrl('/products')" class="text-foreground hover:text-foreground/70 transition-colors">
            {{ t('nav.products') }}
          </Link>
          <Link :href="localizedUrl('/about')" class="text-foreground hover:text-foreground/70 transition-colors">
            {{ t('nav.about') }}
          </Link>
          <Link :href="localizedUrl('/contact')" class="text-foreground hover:text-foreground/70 transition-colors">
            {{ t('nav.contact') }}
          </Link>
        </nav>

        <!-- Cart + Language + Mobile Menu -->
        <div class="flex items-center gap-2">
          <LanguageSwitcher />

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
        <Link
          :href="localizedUrl('/')"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.home') }}
        </Link>
        <Link
          :href="localizedUrl('/products')"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.products') }}
        </Link>
        <Link
          :href="localizedUrl('/about')"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.about') }}
        </Link>
        <Link
          :href="localizedUrl('/contact')"
          class="block text-foreground hover:text-foreground/70 transition-colors"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.contact') }}
        </Link>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ShoppingCart as ShoppingCartIcon, Menu as MenuIcon, X as XIcon } from 'lucide-vue-next'
import Button from '@/Components/UI/Button.vue'
import LanguageSwitcher from '@/Components/Layout/LanguageSwitcher.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

defineProps({
  cartItemsCount: {
    type: Number,
    default: 0
  }
})

defineEmits(['cart-click'])

const { t } = useTranslation()
const { localizedUrl } = useLocale()
const settings = computed(() => usePage().props.site_settings || {})
const isMobileMenuOpen = ref(false)
</script>
