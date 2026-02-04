<template>
  <Head title="Contact Messages" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-medium">Messages</h1>
        <span v-if="unreadCount" class="px-2 py-0.5 rounded-full text-xs bg-primary text-primary-foreground">
          {{ unreadCount }} unread
        </span>
      </div>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Status</th>
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Email</th>
              <th class="p-3 font-medium text-muted-foreground">Subject</th>
              <th class="p-3 font-medium text-muted-foreground">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="msg in messages"
              :key="msg.id"
              :class="['border-b border-border last:border-0', !msg.is_read ? 'bg-primary/5' : '']"
            >
              <td class="p-3">
                <span
                  :class="[
                    'inline-block w-2 h-2 rounded-full',
                    msg.is_read ? 'bg-muted-foreground/30' : 'bg-primary'
                  ]"
                />
              </td>
              <td class="p-3" :class="{ 'font-medium': !msg.is_read }">
                <Link :href="`/admin/contact-messages/${msg.id}`" class="hover:text-primary">
                  {{ msg.name }}
                </Link>
              </td>
              <td class="p-3 text-muted-foreground">{{ msg.email }}</td>
              <td class="p-3">
                <Link :href="`/admin/contact-messages/${msg.id}`" class="hover:text-primary">
                  {{ msg.subject }}
                </Link>
              </td>
              <td class="p-3 text-muted-foreground">{{ formatDate(msg.created_at) }}</td>
            </tr>
            <tr v-if="!messages.length">
              <td colspan="5" class="p-3 text-center text-muted-foreground">No messages yet.</td>
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
  messages: Array,
  unreadCount: Number,
})

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
