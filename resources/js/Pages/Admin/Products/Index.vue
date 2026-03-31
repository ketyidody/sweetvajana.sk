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

    <!-- Flash message -->
    <div v-if="$page.props.flash?.success" class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
      {{ $page.props.flash.success }}
    </div>

    <!-- Import / Export toolbar -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
      <!-- Export -->
      <div class="flex items-center gap-2">
        <select v-model="exportFilter" class="text-sm border border-border rounded-md px-2 py-1.5 bg-background text-foreground">
          <option value="all">All products</option>
          <option value="not_orderable_online">Not orderable online</option>
        </select>
        <a :href="`/admin/products/export?filter=${exportFilter}`" class="inline-flex items-center gap-2 px-3 py-1.5 border border-border rounded-md text-sm hover:bg-muted">
          <DownloadIcon class="w-4 h-4" />
          Export CSV
        </a>
      </div>

      <!-- Import -->
      <div class="flex items-center gap-2">
        <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-border rounded-md text-sm hover:bg-muted cursor-pointer">
          <UploadIcon class="w-4 h-4" />
          Import CSV
          <input type="file" accept=".csv" class="hidden" @change="handleImport" />
        </label>
        <span v-if="importStatus" class="text-sm text-muted-foreground">{{ importStatus }}</span>
      </div>
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
              <th class="p-3 font-medium text-muted-foreground">Active</th>
              <th class="p-3 font-medium text-muted-foreground">Featured</th>
              <th class="p-3 font-medium text-muted-foreground">Collection</th>
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
              <td class="p-3">€{{ product.price }}</td>
              <td class="p-3">
                <span :class="product.is_active ? 'text-green-600' : 'text-red-600'" class="text-xs">
                  {{ product.is_active ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="p-3">
                <span v-if="product.is_featured" class="text-yellow-600 text-xs">Featured</span>
              </td>
              <td class="p-3">
                <span v-if="product.is_available_for_collection" class="text-green-600 text-xs">Yes</span>
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
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Plus as PlusIcon, Pencil as PencilIcon, Trash2 as TrashIcon, Image as ImageIcon, Download as DownloadIcon, Upload as UploadIcon } from 'lucide-vue-next'

defineProps({ products: Array })

const exportFilter = ref('not_orderable_online')
const importStatus = ref('')

function deleteProduct(product) {
  if (confirm(`Delete "${product.name}"?`)) {
    router.delete(`/admin/products/${product.id}`)
  }
}

function handleImport(event) {
  const file = event.target.files[0]
  if (!file) return

  importStatus.value = 'Uploading...'

  const form = new FormData()
  form.append('file', file)
  form.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '')

  router.post('/admin/products/import', form, {
    onSuccess: () => { importStatus.value = '' },
    onError: (errors) => { importStatus.value = errors.file ?? 'Import failed.' },
    onFinish: () => { event.target.value = '' },
  })
}
</script>
