<template>
  <section id="home" class="relative h-[500px] md:h-[600px] overflow-hidden">
    <div class="absolute inset-0">
      <img
        src="https://images.unsplash.com/photo-1542200684142-9e7bf8ce104b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxiYWtlcnklMjBoZXJvfGVufDF8fHx8MTc2ODA2MjY0NXww&ixlib=rb-4.1.0&q=80&w=1080"
        alt="Sweet bakery display"
        class="w-full h-full object-cover"
      >
      <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
      <div class="max-w-2xl text-white">
        <h1 class="text-4xl md:text-5xl lg:text-6xl mb-6">
          Handcrafted Cakes & Sweets
        </h1>
        <p class="text-lg md:text-xl mb-8 text-white/90">
          Discover our delicious selection of freshly baked cakes, pastries, and sweet treats made with love.
        </p>
        <a
          href="#products"
          class="inline-block bg-white text-primary px-8 py-3 rounded-lg hover:bg-white/90 transition-colors"
        >
          Shop Now
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
//
</script>
