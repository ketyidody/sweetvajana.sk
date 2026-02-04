<template>
  <Head title="Orders" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Orders</h1>
    </div>

    <div class="flex gap-2 mb-4">
      <Link
        v-for="s in statuses"
        :key="s.value"
        :href="`/admin/orders${s.value === 'all' ? '' : `?status=${s.value}`}`"
        :class="[
          'px-3 py-1 rounded-full text-xs border transition-colors',
          currentStatus === s.value
            ? 'bg-primary text-primary-foreground border-primary'
            : 'border-border text-muted-foreground hover:text-foreground'
        ]"
      >
        {{ s.label }}
      </Link>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Order #</th>
              <th class="p-3 font-medium text-muted-foreground">Customer</th>
              <th class="p-3 font-medium text-muted-foreground">Total</th>
              <th class="p-3 font-medium text-muted-foreground">Status</th>
              <th class="p-3 font-medium text-muted-foreground">Payment</th>
              <th class="p-3 font-medium text-muted-foreground">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id" class="border-b border-border last:border-0">
              <td class="p-3">
                <Link :href="`/admin/orders/${order.id}`" class="text-primary hover:underline">
                  {{ order.order_number }}
                </Link>
              </td>
              <td class="p-3">{{ order.customer_name }}</td>
              <td class="p-3">${{ order.total }}</td>
              <td class="p-3">
                <span :class="statusClass(order.status)" class="px-2 py-1 rounded-full text-xs">
                  {{ order.status }}
                </span>
              </td>
              <td class="p-3">
                <span :class="paymentClass(order.payment_status)" class="text-xs">
                  {{ order.payment_status }}
                </span>
              </td>
              <td class="p-3 text-muted-foreground">{{ formatDate(order.created_at) }}</td>
            </tr>
            <tr v-if="!orders.length">
              <td colspan="6" class="p-3 text-center text-muted-foreground">No orders found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  orders: Array,
  currentStatus: String,
})

const statuses = [
  { value: 'all', label: 'All' },
  { value: 'pending', label: 'Pending' },
  { value: 'processing', label: 'Processing' },
  { value: 'shipped', label: 'Shipped' },
  { value: 'delivered', label: 'Delivered' },
  { value: 'cancelled', label: 'Cancelled' },
]

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

function paymentClass(status) {
  return status === 'paid' ? 'text-green-600' : status === 'failed' ? 'text-red-600' : 'text-muted-foreground'
}

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
