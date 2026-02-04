<template>
  <Head title="Users" />
  <AdminLayout>
    <h1 class="text-2xl font-medium mb-6">Users</h1>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Email</th>
              <th class="p-3 font-medium text-muted-foreground">Role</th>
              <th class="p-3 font-medium text-muted-foreground">Orders</th>
              <th class="p-3 font-medium text-muted-foreground">Joined</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-b border-border last:border-0">
              <td class="p-3 font-medium">{{ user.name }}</td>
              <td class="p-3 text-muted-foreground">{{ user.email }}</td>
              <td class="p-3">
                <span v-if="user.is_admin" class="px-2 py-1 rounded-full text-xs bg-primary text-primary-foreground">Admin</span>
                <span v-else class="text-xs text-muted-foreground">User</span>
              </td>
              <td class="p-3">{{ user.orders_count }}</td>
              <td class="p-3 text-muted-foreground">{{ formatDate(user.created_at) }}</td>
              <td class="p-3">
                <Link :href="`/admin/users/${user.id}`" class="text-muted-foreground hover:text-foreground">
                  <EyeIcon class="w-4 h-4" />
                </Link>
              </td>
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
import { Eye as EyeIcon } from 'lucide-vue-next'

defineProps({ users: Array })

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
