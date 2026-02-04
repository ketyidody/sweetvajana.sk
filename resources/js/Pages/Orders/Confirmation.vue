<template>
  <Head title="Order Confirmed" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit('/cart')" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="max-w-2xl mx-auto">
        <!-- Success Header -->
        <div class="text-center mb-8">
          <CheckCircleIcon class="w-16 h-16 mx-auto text-green-600 mb-4" />
          <h1 class="text-3xl md:text-4xl mb-2">Order Confirmed!</h1>
          <p class="text-muted-foreground">
            Thank you for your order. Your order number is
            <span class="font-medium text-foreground">{{ order.order_number }}</span>
          </p>
        </div>

        <!-- Order Details -->
        <div class="bg-card rounded-lg border border-border p-6 mb-6">
          <h2 class="text-lg font-medium mb-4">Order Details</h2>
          <div class="space-y-3">
            <div
              v-for="item in order.items"
              :key="item.product_name"
              class="flex justify-between items-center text-sm"
            >
              <div>
                <p>{{ item.product_name }}</p>
                <p class="text-muted-foreground">Qty: {{ item.quantity }} × ${{ item.price }}</p>
              </div>
              <p>${{ item.subtotal }}</p>
            </div>
          </div>
          <div class="border-t border-border mt-4 pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-muted-foreground">Subtotal</span>
              <span>${{ order.subtotal }}</span>
            </div>
            <div v-if="Number(order.shipping) > 0" class="flex justify-between">
              <span class="text-muted-foreground">Shipping</span>
              <span>${{ order.shipping }}</span>
            </div>
            <div v-else class="flex justify-between">
              <span class="text-muted-foreground">Shipping</span>
              <span>Free</span>
            </div>
            <div class="border-t border-border pt-2 mt-2 flex justify-between font-medium text-base">
              <span>Total</span>
              <span>${{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-card rounded-lg border border-border p-6 mb-8">
          <h2 class="text-lg font-medium mb-4">Delivery Information</h2>
          <div class="space-y-2 text-sm">
            <p><span class="text-muted-foreground">Name:</span> {{ order.customer_name }}</p>
            <p><span class="text-muted-foreground">Email:</span> {{ order.customer_email }}</p>
            <p><span class="text-muted-foreground">Phone:</span> {{ order.customer_phone }}</p>
            <p><span class="text-muted-foreground">Address:</span> {{ order.shipping_address }}</p>
            <p v-if="order.notes"><span class="text-muted-foreground">Notes:</span> {{ order.notes }}</p>
          </div>
        </div>

        <div class="text-center">
          <Link
            href="/products"
            class="inline-flex items-center justify-center px-6 py-2.5 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors text-sm font-medium"
          >
            Continue Shopping
          </Link>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { CheckCircle as CheckCircleIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'

defineProps({
  order: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)
</script>
