<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
    :class="isTransparent
      ? 'bg-transparent'
      : 'bg-background border-b border-border'"
  >
    <div v-if="!isTransparent" class="absolute inset-0 bg-pink-500/10 pointer-events-none transition-opacity duration-500"></div>
    <div class="relative container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 md:h-20">
        <!-- Logo -->
        <Link :href="localizedUrl('/')" class="flex-shrink-0">
          <img v-if="settings.logo" :src="settings.logo" :alt="settings.site_name || 'Sweet Vajana'" class="h-12 md:h-15 w-auto object-contain" />
          <span v-else class="text-xl md:text-2xl tracking-tight transition-colors duration-500" :class="isTransparent ? 'text-white' : ''">{{ settings.site_name || 'Sweet Vajana' }}</span>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <Link :href="localizedUrl('/')" :class="navLinkClass">
            {{ t('nav.home') }}
          </Link>
          <Link :href="localizedUrl('/products')" :class="navLinkClass">
            {{ t('nav.products') }}
          </Link>
          <Link :href="localizedUrl('/about')" :class="navLinkClass">
            {{ t('nav.about') }}
          </Link>
          <Link :href="localizedUrl('/contact')" :class="navLinkClass">
            {{ t('nav.contact') }}
          </Link>
        </nav>

        <!-- Cart + Language + Mobile Menu -->
        <div class="flex items-center gap-2">
          <LanguageSwitcher :transparent="isTransparent" />

          <Button
            :variant="isTransparent ? 'ghost' : 'outline'"
            size="icon"
            class="relative transition-colors duration-500"
            :class="isTransparent ? 'text-white hover:text-white/70 hover:bg-white/10' : ''"
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
            class="md:hidden p-2 transition-colors duration-500"
            :class="isTransparent ? 'text-white' : ''"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
          >
            <XIcon v-if="isMobileMenuOpen" class="h-6 w-6" />
            <MenuIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <nav v-if="isMobileMenuOpen" class="md:hidden py-4 space-y-4" :class="isTransparent ? 'border-t border-white/20' : 'border-t border-border'">
        <Link
          :href="localizedUrl('/')"
          class="block"
          :class="navLinkClass"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.home') }}
        </Link>
        <Link
          :href="localizedUrl('/products')"
          class="block"
          :class="navLinkClass"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.products') }}
        </Link>
        <Link
          :href="localizedUrl('/about')"
          class="block"
          :class="navLinkClass"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.about') }}
        </Link>
        <Link
          :href="localizedUrl('/contact')"
          class="block"
          :class="navLinkClass"
          @click="isMobileMenuOpen = false"
        >
          {{ t('nav.contact') }}
        </Link>
      </nav>
    </div>
  </header>
  <!-- Spacer to prevent content from hiding behind fixed header on non-transparent pages -->
  <div v-if="!transparent" class="h-16 md:h-20"></div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ShoppingCart as ShoppingCartIcon, Menu as MenuIcon, X as XIcon } from 'lucide-vue-next'
import Button from '@/Components/UI/Button.vue'
import LanguageSwitcher from '@/Components/Layout/LanguageSwitcher.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const props = defineProps({
  cartItemsCount: {
    type: Number,
    default: 0
  },
  transparent: {
    type: Boolean,
    default: false
  }
})

defineEmits(['cart-click'])

const { t } = useTranslation()
const { localizedUrl } = useLocale()
const settings = computed(() => usePage().props.site_settings || {})
const isMobileMenuOpen = ref(false)

const scrolled = ref(false)

function onScroll() {
  scrolled.value = window.scrollY > 50
}

onMounted(() => {
  if (props.transparent) {
    window.addEventListener('scroll', onScroll, { passive: true })
    onScroll()
  }
})

onUnmounted(() => {
  if (props.transparent) {
    window.removeEventListener('scroll', onScroll)
  }
})

const isTransparent = computed(() => props.transparent && !scrolled.value)

const navLinkClass = computed(() =>
  isTransparent.value
    ? 'text-white hover:text-white/70 transition-colors duration-500'
    : 'text-foreground hover:text-foreground/70 transition-colors duration-500'
)
</script>
