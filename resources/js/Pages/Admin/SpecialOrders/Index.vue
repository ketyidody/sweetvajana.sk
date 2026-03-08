<template>
  <Head title="Special Orders" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Special Orders</h1>
    </div>

    <div class="flex gap-2 mb-4">
      <Link
        v-for="s in statuses"
        :key="s.value"
        :href="`/admin/special-orders${s.value === 'all' ? '' : `?status=${s.value}`}`"
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
              <th class="p-3 font-medium text-muted-foreground">#</th>
              <th class="p-3 font-medium text-muted-foreground">Product</th>
              <th class="p-3 font-medium text-muted-foreground">Customer</th>
              <th class="p-3 font-medium text-muted-foreground">Email</th>
              <th class="p-3 font-medium text-muted-foreground">Status</th>
              <th class="p-3 font-medium text-muted-foreground">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in specialOrders" :key="order.id" class="border-b border-border last:border-0 cursor-pointer hover:bg-muted/50 transition-colors" @click="router.visit(`/admin/special-orders/${order.id}`)">
              <td class="p-3 text-primary">{{ order.id }}</td>
              <td class="p-3">{{ order.product_name }}</td>
              <td class="p-3">{{ order.customer_name }}</td>
              <td class="p-3 text-muted-foreground">{{ order.customer_email }}</td>
              <td class="p-3">
                <span :class="statusClass(order.status)" class="px-2 py-1 rounded-full text-xs">
                  {{ order.status }}
                </span>
              </td>
              <td class="p-3 text-muted-foreground">{{ formatDate(order.created_at) }}</td>
            </tr>
            <tr v-if="!specialOrders.length">
              <td colspan="6" class="p-3 text-center text-muted-foreground">No special orders found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  specialOrders: Array,
  currentStatus: String,
})

const statuses = [
  { value: 'all', label: 'All' },
  { value: 'new', label: 'New' },
  { value: 'contacted', label: 'Contacted' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
]

function statusClass(status) {
  const classes = {
    new: 'bg-yellow-100 text-yellow-800',
    contacted: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
