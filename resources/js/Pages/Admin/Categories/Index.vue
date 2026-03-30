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
              <th class="p-3 w-8"></th>
              <th class="p-3 font-medium text-muted-foreground">Image</th>
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Products</th>
              <th class="p-3 font-medium text-muted-foreground">Active</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="category in categories"
              :key="category.id"
              draggable="true"
              @dragstart="onDragStart($event, category)"
              @dragover="onDragOver($event, category)"
              @dragleave="onDragLeave"
              @drop="onDrop($event, category)"
              @dragend="onDragEnd"
              class="border-b border-border last:border-0 transition-colors"
              :class="{
                'opacity-40': draggedId === category.id,
                'border-t-2 border-t-primary bg-muted/30': dragOverId === category.id && dropBefore,
                'border-b-2 border-b-primary bg-muted/30': dragOverId === category.id && !dropBefore,
              }"
            >
              <td class="p-3 cursor-grab active:cursor-grabbing text-muted-foreground">
                <GripVerticalIcon class="w-4 h-4" />
              </td>
              <td class="p-3">
                <img v-if="category.image" :src="category.image" :alt="category.name" class="w-10 h-10 rounded object-cover" />
                <div v-else class="w-10 h-10 rounded bg-muted flex items-center justify-center">
                  <ImageIcon class="w-4 h-4 text-muted-foreground" />
                </div>
              </td>
              <td class="p-3 font-medium">
                <div class="flex items-center" :style="{ paddingLeft: (category.depth || 0) * 24 + 'px' }">
                  <CornerDownRightIcon v-if="category.depth > 0" class="w-4 h-4 text-muted-foreground mr-2 flex-shrink-0" />
                  {{ category.name }}
                </div>
              </td>
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
              <td colspan="6" class="p-3 text-center text-muted-foreground">No categories yet.</td>
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
import {
  Plus as PlusIcon,
  Pencil as PencilIcon,
  Trash2 as TrashIcon,
  Image as ImageIcon,
  CornerDownRight as CornerDownRightIcon,
  GripVertical as GripVerticalIcon,
} from 'lucide-vue-next'

const props = defineProps({ categories: Array })

const draggedId = ref(null)
const dragOverId = ref(null)
const dropBefore = ref(true)

function onDragStart(e, category) {
  draggedId.value = category.id
  e.dataTransfer.effectAllowed = 'move'
}

function onDragOver(e, category) {
  if (!draggedId.value) return

  const dragged = props.categories.find(c => c.id === draggedId.value)
  // Only allow reordering within the same parent
  if (dragged.parent_id !== category.parent_id || dragged.id === category.id) return

  e.preventDefault()
  e.dataTransfer.dropEffect = 'move'
  dragOverId.value = category.id

  const rect = e.currentTarget.getBoundingClientRect()
  dropBefore.value = e.clientY < rect.top + rect.height / 2
}

function onDragLeave() {
  dragOverId.value = null
}

function onDrop(e, targetCategory) {
  e.preventDefault()
  dragOverId.value = null

  if (!draggedId.value || draggedId.value === targetCategory.id) return

  const dragged = props.categories.find(c => c.id === draggedId.value)
  if (dragged.parent_id !== targetCategory.parent_id) return

  // Reorder siblings: remove dragged, insert at target position
  const siblings = props.categories.filter(c => c.parent_id === dragged.parent_id)
  const without = siblings.filter(c => c.id !== dragged.id)
  let insertAt = without.findIndex(c => c.id === targetCategory.id)
  if (!dropBefore.value) insertAt++
  without.splice(insertAt, 0, dragged)

  router.post('/admin/categories/reorder', {
    ids: without.map(c => c.id),
  }, { preserveScroll: true })
}

function onDragEnd() {
  draggedId.value = null
  dragOverId.value = null
}

function deleteCategory(category) {
  const msg = `Delete "${category.name}"? Its subcategories will be moved to its parent.`
  if (confirm(msg)) {
    router.delete(`/admin/categories/${category.id}`)
  }
}
</script>
