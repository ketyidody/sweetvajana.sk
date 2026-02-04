<template>
  <Head title="Pages" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Pages</h1>
      <Link href="/admin/pages/create" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
        <PlusIcon class="w-4 h-4" />
        Add Page
      </Link>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Title</th>
              <th class="p-3 font-medium text-muted-foreground">Slug</th>
              <th class="p-3 font-medium text-muted-foreground">Active</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="page in pages" :key="page.id" class="border-b border-border last:border-0">
              <td class="p-3 font-medium">{{ page.title }}</td>
              <td class="p-3 text-muted-foreground">/{{ page.slug }}</td>
              <td class="p-3">
                <span :class="page.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs">
                  {{ page.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <Link :href="`/admin/pages/${page.id}/edit`" class="text-muted-foreground hover:text-foreground">
                    <PencilIcon class="w-4 h-4" />
                  </Link>
                  <button @click="deletePage(page)" class="text-muted-foreground hover:text-destructive">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!pages.length">
              <td colspan="4" class="p-3 text-center text-muted-foreground">No pages yet.</td>
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
import { Plus as PlusIcon, Pencil as PencilIcon, Trash2 as TrashIcon } from 'lucide-vue-next'

defineProps({ pages: Array })

function deletePage(page) {
  if (confirm(`Delete "${page.title}"?`)) {
    router.delete(`/admin/pages/${page.id}`)
  }
}
</script>
