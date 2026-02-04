<template>
  <Head title="Products" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Products</h1>
      <Link href="/admin/products/create" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
        <PlusIcon class="w-4 h-4" />
        Add Product
      </Link>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 font-medium text-muted-foreground">Image</th>
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Category</th>
              <th class="p-3 font-medium text-muted-foreground">Price</th>
              <th class="p-3 font-medium text-muted-foreground">Stock</th>
              <th class="p-3 font-medium text-muted-foreground">Active</th>
              <th class="p-3 font-medium text-muted-foreground">Featured</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in products" :key="product.id" class="border-b border-border last:border-0">
              <td class="p-3">
                <img v-if="product.image" :src="product.image" :alt="product.name" class="w-10 h-10 rounded object-cover" />
                <div v-else class="w-10 h-10 rounded bg-muted flex items-center justify-center">
                  <ImageIcon class="w-4 h-4 text-muted-foreground" />
                </div>
              </td>
              <td class="p-3 font-medium">{{ product.name }}</td>
              <td class="p-3 text-muted-foreground">{{ product.category?.name }}</td>
              <td class="p-3">${{ product.price }}</td>
              <td class="p-3">{{ product.stock }}</td>
              <td class="p-3">
                <span :class="product.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs">
                  {{ product.is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="p-3">
                <span v-if="product.is_featured" class="text-yellow-600 text-xs">Featured</span>
              </td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <Link :href="`/admin/products/${product.id}/edit`" class="text-muted-foreground hover:text-foreground">
                    <PencilIcon class="w-4 h-4" />
                  </Link>
                  <button @click="deleteProduct(product)" class="text-muted-foreground hover:text-destructive">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!products.length">
              <td colspan="8" class="p-3 text-center text-muted-foreground">No products yet.</td>
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

defineProps({ products: Array })

function deleteProduct(product) {
  if (confirm(`Delete "${product.name}"?`)) {
    router.delete(`/admin/products/${product.id}`)
  }
}
</script>
