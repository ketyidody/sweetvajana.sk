<template>
  <section id="home" class="relative h-[500px] md:h-[600px] overflow-hidden">
    <div class="absolute inset-0">
      <img
        :src="heroImage"
        :alt="heroTitle"
        class="w-full h-full object-cover"
      >
      <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
      <div class="max-w-2xl text-white">
        <h1 class="text-4xl md:text-5xl lg:text-6xl mb-6">
          {{ heroTitle }}
        </h1>
        <p class="text-lg md:text-xl mb-8 text-white/90">
          {{ heroSubtitle }}
        </p>
        <a
          href="#products"
          class="inline-block bg-white text-primary px-8 py-3 rounded-lg hover:bg-white/90 transition-colors"
        >
          {{ heroCtaText }}
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()
const settings = computed(() => usePage().props.site_settings || {})

const heroTitle = computed(() => settings.value.hero_title || t('product.featured'))
const heroSubtitle = computed(() => settings.value.hero_subtitle || t('product.featured_subtitle'))
const heroImage = computed(() => settings.value.hero_image || 'https://images.unsplash.com/photo-1542200684142-9e7bf8ce104b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxiYWtlcnklMjBoZXJvfGVufDF8fHx8MTc2ODA2MjY0NXww&ixlib=rb-4.1.0&q=80&w=1080')
const heroCtaText = computed(() => settings.value.hero_cta_text || t('common.shop_now'))
</script>
