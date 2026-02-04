<template>
  <Head title="Categories" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Categories</h1>
      <Link href="/admin/categories/create" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
        <PlusIcon class="w-4 h-4" />
        Add Category
      </Link>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Image</th>
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Products</th>
              <th class="p-3 font-medium text-muted-foreground">Active</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id" class="border-b border-border last:border-0">
              <td class="p-3">
                <img v-if="category.image" :src="category.image" :alt="category.name" class="w-10 h-10 rounded object-cover" />
                <div v-else class="w-10 h-10 rounded bg-muted flex items-center justify-center">
                  <ImageIcon class="w-4 h-4 text-muted-foreground" />
                </div>
              </td>
              <td class="p-3 font-medium">{{ category.name }}</td>
              <td class="p-3">{{ category.products_count }}</td>
              <td class="p-3">
                <span :class="category.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs">
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <Link :href="`/admin/categories/${category.id}/edit`" class="text-muted-foreground hover:text-foreground">
                    <PencilIcon class="w-4 h-4" />
                  </Link>
                  <button @click="deleteCategory(category)" class="text-muted-foreground hover:text-destructive">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!categories.length">
              <td colspan="5" class="p-3 text-center text-muted-foreground">No categories yet.</td>
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
import { Plus as PlusIcon, Pencil as PencilIcon, Trash2 as TrashIcon, Image as ImageIcon } from 'lucide-vue-next'

defineProps({ categories: Array })

function deleteCategory(category) {
  if (confirm(`Delete "${category.name}"?`)) {
    router.delete(`/admin/categories/${category.id}`)
  }
}
</script>
