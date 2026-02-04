<template>
  <Head :title="`Order ${order.order_number}`" />
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <Link href="/account/orders" class="text-sm text-muted-foreground hover:text-foreground mb-4 inline-block">&larr; Back to orders</Link>
      <h1 class="text-2xl font-medium mb-6">Order {{ order.order_number }}</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <div class="bg-card rounded-lg border border-border">
            <div class="p-4 border-b border-border">
              <h2 class="font-medium">Items</h2>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-border text-left">
                    <th class="p-3 font-medium text-muted-foreground">Product</th>
                    <th class="p-3 font-medium text-muted-foreground">Price</th>
                    <th class="p-3 font-medium text-muted-foreground">Qty</th>
                    <th class="p-3 font-medium text-muted-foreground text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in order.items" :key="item.id" class="border-b border-border last:border-0">
                    <td class="p-3">{{ item.product_name }}</td>
                    <td class="p-3">${{ item.price }}</td>
                    <td class="p-3">{{ item.quantity }}</td>
                    <td class="p-3 text-right">${{ item.subtotal }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="border-t border-border">
                    <td colspan="3" class="p-3 text-right text-muted-foreground">Subtotal</td>
                    <td class="p-3 text-right">${{ order.subtotal }}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="p-3 text-right text-muted-foreground">Tax</td>
                    <td class="p-3 text-right">${{ order.tax }}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="p-3 text-right text-muted-foreground">Shipping</td>
                    <td class="p-3 text-right">${{ order.shipping }}</td>
                  </tr>
                  <tr class="font-medium">
                    <td colspan="3" class="p-3 text-right">Total</td>
                    <td class="p-3 text-right">${{ order.total }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-card rounded-lg border border-border p-4">
            <h2 class="font-medium mb-3">Order Status</h2>
            <span :class="statusClass(order.status)" class="px-2 py-1 rounded-full text-xs">
              {{ order.status }}
            </span>
          </div>
          <div class="bg-card rounded-lg border border-border p-4">
            <h2 class="font-medium mb-3">Shipping Address</h2>
            <p class="text-sm whitespace-pre-line text-muted-foreground">{{ order.shipping_address }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({ order: Object })

function statusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>
