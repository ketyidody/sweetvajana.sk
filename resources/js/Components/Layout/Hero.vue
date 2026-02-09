<template>
  <section id="home" class="relative h-screen overflow-hidden">
    <div class="absolute inset-0">
      <div v-for="(img, i) in images" :key="i" class="absolute inset-0 transition-opacity duration-1000" :class="i === current ? 'opacity-100' : 'opacity-0'">
        <img
          :src="img"
          :alt="heroTitle"
          class="w-full h-full object-cover"
        >
      </div>
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

    <!-- Progress bar -->
    <div v-if="images.length > 1" class="absolute bottom-0 left-0 right-0 h-[2px]">
      <div
        class="h-full"
        :style="{ width: progressWidth, backgroundColor: '#c2b59b', transition: progressTransition }"
      />
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()
const settings = computed(() => usePage().props.site_settings || {})

const heroTitle = computed(() => settings.value.hero_title || t('product.featured'))
const heroSubtitle = computed(() => settings.value.hero_subtitle || t('product.featured_subtitle'))
const heroCtaText = computed(() => settings.value.hero_cta_text || t('common.shop_now'))

const defaultImage = 'https://images.unsplash.com/photo-1542200684142-9e7bf8ce104b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxiYWtlcnklMjBoZXJvfGVufDF8fHx8MTc2ODA2MjY0NXww&ixlib=rb-4.1.0&q=80&w=1080'

const images = computed(() => {
  const imgs = settings.value.hero_images
  if (Array.isArray(imgs) && imgs.length > 0) return imgs
  return [defaultImage]
})

const current = ref(0)
const progress = ref(0)
let timer = null
let progressTimer = null

const INTERVAL = 5000
const TICK = 50

const progressWidth = computed(() => `${progress.value}%`)
const progressTransition = computed(() =>
  progress.value === 0 ? 'none' : `width ${TICK}ms linear`
)

function next() {
  progress.value = 0
  current.value = (current.value + 1) % images.value.length
}

function startTimer() {
  if (images.value.length <= 1) return
  progressTimer = setInterval(() => {
    progress.value += (TICK / INTERVAL) * 100
  }, TICK)
  timer = setInterval(next, INTERVAL)
}

function stopTimer() {
  if (timer) { clearInterval(timer); timer = null }
  if (progressTimer) { clearInterval(progressTimer); progressTimer = null }
}

onMounted(() => startTimer())
onUnmounted(() => stopTimer())
</script>
