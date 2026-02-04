<template>
  <Head :title="`Message from ${message.name}`" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <div>
        <Link href="/admin/contact-messages" class="text-sm text-muted-foreground hover:text-foreground mb-2 inline-block">&larr; Back to messages</Link>
        <h1 class="text-2xl font-medium">{{ message.subject }}</h1>
      </div>
      <button
        @click="deleteMessage"
        class="px-4 py-2 bg-destructive text-destructive-foreground rounded-md text-sm hover:bg-destructive/90"
      >
        Delete
      </button>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="p-4 border-b border-border">
        <dl class="flex flex-wrap gap-x-8 gap-y-2 text-sm">
          <div>
            <dt class="text-muted-foreground">From</dt>
            <dd class="font-medium">{{ message.name }}</dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Email</dt>
            <dd>
              <a :href="`mailto:${message.email}`" class="text-primary hover:underline">{{ message.email }}</a>
            </dd>
          </div>
          <div>
            <dt class="text-muted-foreground">Date</dt>
            <dd>{{ formatDate(message.created_at) }}</dd>
          </div>
        </dl>
      </div>
      <div class="p-4">
        <p class="text-sm whitespace-pre-wrap">{{ message.message }}</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ message: Object })

function formatDate(date) {
  return new Date(date).toLocaleString()
}

function deleteMessage() {
  if (confirm('Are you sure you want to delete this message?')) {
    router.delete(`/admin/contact-messages/${props.message.id}`)
  }
}
</script>
