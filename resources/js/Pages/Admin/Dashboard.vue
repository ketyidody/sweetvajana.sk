<template>
  <Head title="Admin Dashboard" />
  <AdminLayout>
    <h1 class="text-2xl font-medium mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
      <div v-for="stat in statCards" :key="stat.label" class="bg-card rounded-lg border border-border p-4">
        <p class="text-sm text-muted-foreground">{{ stat.label }}</p>
        <p class="text-2xl font-medium mt-1">{{ stat.value }}</p>
      </div>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="p-4 border-b border-border">
        <h2 class="font-medium">Recent Orders</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Order #</th>
              <th class="p-3 font-medium text-muted-foreground">Customer</th>
              <th class="p-3 font-medium text-muted-foreground">Total</th>
              <th class="p-3 font-medium text-muted-foreground">Status</th>
              <th class="p-3 font-medium text-muted-foreground">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in recentOrders" :key="order.id" class="border-b border-border last:border-0">
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
              <td class="p-3 text-muted-foreground">{{ formatDate(order.created_at) }}</td>
            </tr>
            <tr v-if="!recentOrders.length">
              <td colspan="5" class="p-3 text-center text-muted-foreground">No orders yet.</td>
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

const props = defineProps({
  stats: Object,
  recentOrders: Array,
})

const statCards = [
  { label: 'Total Orders', value: props.stats.totalOrders },
  { label: 'Revenue', value: `$${Number(props.stats.totalRevenue).toFixed(2)}` },
  { label: 'Products', value: props.stats.totalProducts },
  { label: 'Categories', value: props.stats.totalCategories },
  { label: 'Users', value: props.stats.totalUsers },
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

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
