<template>
  <Head :title="page.title" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit('/cart')" />

    <main class="flex-1">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 max-w-3xl">
        <h1 class="text-3xl font-medium mb-8">{{ page.title }}</h1>
        <div class="prose prose-neutral max-w-none text-foreground" v-html="renderedContent" />
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'

const props = defineProps({
  page: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)

const renderedContent = computed(() => {
  if (!props.page.content) return ''
  return props.page.content
    .split('\n\n')
    .map(p => p.trim())
    .filter(Boolean)
    .map(p => `<p>${p}</p>`)
    .join('')
})
</script>
