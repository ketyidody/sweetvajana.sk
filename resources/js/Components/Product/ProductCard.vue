<template>
  <div
    class="group cursor-pointer bg-card rounded-lg overflow-hidden border border-border hover:shadow-lg transition-all duration-300"
    @click="$emit('click')"
  >
    <div class="aspect-square overflow-hidden bg-muted">
      <img
        :src="product.image"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
    </div>
    <div class="p-4">
      <div class="flex items-start justify-between gap-2 mb-2">
        <h3 class="line-clamp-1">{{ product.name }}</h3>
        <span class="text-primary flex-shrink-0">${{ product.price }}</span>
      </div>
      <p class="text-muted-foreground line-clamp-2 text-sm">
        {{ product.description }}
      </p>
      <div class="mt-3">
        <span class="inline-block px-3 py-1 bg-muted text-muted-foreground rounded-full text-xs">
          {{ product.category }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['click'])
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
