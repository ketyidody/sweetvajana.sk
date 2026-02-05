<template>
  <Head title="Languages" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Languages</h1>
      <Link
        href="/admin/languages/create"
        class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90"
      >
        Add Language
      </Link>
    </div>

    <div class="bg-card rounded-lg border border-border overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-muted/50">
          <tr>
            <th class="text-left px-4 py-3 font-medium">Code</th>
            <th class="text-left px-4 py-3 font-medium">Name</th>
            <th class="text-left px-4 py-3 font-medium">Native Name</th>
            <th class="text-center px-4 py-3 font-medium">Default</th>
            <th class="text-center px-4 py-3 font-medium">Active</th>
            <th class="text-center px-4 py-3 font-medium">Order</th>
            <th class="text-right px-4 py-3 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <tr v-for="lang in languages" :key="lang.id">
            <td class="px-4 py-3 font-mono">{{ lang.code }}</td>
            <td class="px-4 py-3">{{ lang.name }}</td>
            <td class="px-4 py-3">{{ lang.native_name }}</td>
            <td class="px-4 py-3 text-center">
              <span v-if="lang.is_default" class="text-green-600">Yes</span>
              <span v-else class="text-muted-foreground">No</span>
            </td>
            <td class="px-4 py-3 text-center">
              <span v-if="lang.is_active" class="text-green-600">Yes</span>
              <span v-else class="text-destructive">No</span>
            </td>
            <td class="px-4 py-3 text-center">{{ lang.sort_order }}</td>
            <td class="px-4 py-3 text-right space-x-2">
              <Link
                :href="`/admin/languages/${lang.id}/edit`"
                class="text-primary hover:text-primary/80 text-sm"
              >
                Edit
              </Link>
              <button
                v-if="!lang.is_default"
                @click="deleteLanguage(lang.id)"
                class="text-destructive hover:text-destructive/80 text-sm"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  languages: Array,
})

function deleteLanguage(id) {
  if (confirm('Are you sure you want to delete this language?')) {
    router.delete(`/admin/languages/${id}`)
  }
}
</script>
