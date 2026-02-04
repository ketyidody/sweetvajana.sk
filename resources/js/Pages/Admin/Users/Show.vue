<template>
  <Head :title="`User: ${user.name}`" />
  <AdminLayout>
    <Link href="/admin/users" class="text-sm text-muted-foreground hover:text-foreground mb-2 inline-block">&larr; Back to users</Link>
    <h1 class="text-2xl font-medium mb-6">{{ user.name }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- User details -->
      <div class="bg-card rounded-lg border border-border p-4">
        <h2 class="font-medium mb-3">Details</h2>
        <dl class="text-sm space-y-2">
          <div>
            <dt class="text-muted-foreground">Email</dt>
            <dd>{{ user.email }}</dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Phone</dt>
            <dd>{{ user.phone || '-' }}</dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Orders</dt>
            <dd>{{ user.orders_count }}</dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Joined</dt>
            <dd>{{ formatDate(user.created_at) }}</dd>
          </div>
        </dl>

        <div class="mt-4 pt-4 border-t border-border space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-sm">Admin</span>
            <button
              @click="toggleAdmin"
              :class="[
                'px-3 py-1 rounded-md text-xs',
                user.is_admin ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'
              ]"
            >
              {{ user.is_admin ? 'Yes' : 'No' }}
            </button>
          </div>
          <button
            @click="deleteUser"
            class="w-full px-3 py-2 text-sm text-destructive border border-destructive/30 rounded-md hover:bg-destructive/10"
          >
            Delete User
          </button>
        </div>
      </div>

      <!-- Order history -->
      <div class="lg:col-span-2 bg-card rounded-lg border border-border">
        <div class="p-4 border-b border-border">
          <h2 class="font-medium">Order History</h2>
        </div>
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
                  <Link :href="`/admin/orders/${order.id}`" class="text-primary hover:underline">
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
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  user: Object,
  orders: Array,
})

function toggleAdmin() {
  router.patch(`/admin/users/${props.user.id}`, {
    is_admin: !props.user.is_admin,
  })
}

function deleteUser() {
  if (confirm(`Delete "${props.user.name}"? This cannot be undone.`)) {
    router.delete(`/admin/users/${props.user.id}`)
  }
}

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
