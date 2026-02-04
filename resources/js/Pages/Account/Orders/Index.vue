<template>
  <Head title="My Orders" />
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-medium">My Orders</h1>
        <Link href="/" class="text-sm text-muted-foreground hover:text-foreground">&larr; Back to shop</Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <nav class="space-y-1">
          <Link href="/account" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            Overview
          </Link>
          <Link href="/account/orders" class="block px-3 py-2 rounded-md text-sm bg-accent text-accent-foreground">
            My Orders
          </Link>
          <Link href="/account/addresses" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            Addresses
          </Link>
          <Link href="/profile" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            Profile Settings
          </Link>
        </nav>

        <div class="md:col-span-3">
          <div class="bg-card rounded-lg border border-border">
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-border text-left">
                    <th class="p-3 font-medium text-muted-foreground">Order #</th>
                    <th class="p-3 font-medium text-muted-foreground">Total</th>
                    <th class="p-3 font-medium text-muted-foreground">Status</th>
                    <th class="p-3 font-medium text-muted-foreground">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in orders" :key="order.id" class="border-b border-border last:border-0">
                    <td class="p-3">
                      <Link :href="`/account/orders/${order.id}`" class="text-primary hover:underline">
                        {{ order.order_number }}
                      </Link>
                    </td>
                    <td class="p-3">${{ order.total }}</td>
                    <td class="p-3">
                      <span :class="statusClass(order.status)" class="px-2 py-1 rounded-full text-xs">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="p-3 text-muted-foreground">{{ formatDate(order.created_at) }}</td>
                  </tr>
                  <tr v-if="!orders.length">
                    <td colspan="4" class="p-3 text-center text-muted-foreground">No orders yet.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({ orders: Array })

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

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
