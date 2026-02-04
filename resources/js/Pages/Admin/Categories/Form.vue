<template>
  <Head :title="category ? 'Edit Category' : 'New Category'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ category ? 'Edit Category' : 'New Category' }}</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.description" class="text-destructive text-xs mt-1">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Image</label>
          <input
            type="file"
            accept="image/*"
            @change="form.image = $event.target.files[0]"
            class="w-full text-sm"
          />
          <img v-if="category?.image" :src="category.image" class="mt-2 w-20 h-20 rounded object-cover" />
          <p v-if="form.errors.image" class="text-destructive text-xs mt-1">{{ form.errors.image }}</p>
        </div>

        <div class="flex items-center gap-2">
          <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
          <label for="is_active" class="text-sm">Active</label>
        </div>

        <div class="flex items-center gap-3 pt-4">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
          >
            {{ category ? 'Update' : 'Create' }}
          </button>
          <Link href="/admin/categories" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  category: { type: Object, default: null },
})

const form = useForm({
  name: props.category?.name ?? '',
  description: props.category?.description ?? '',
  image: null,
  is_active: props.category?.is_active ?? true,
})

function submit() {
  if (props.category) {
    form.post(`/admin/categories/${props.category.id}`, {
      forceFormData: true,
      headers: { 'X-HTTP-Method-Override': 'PUT' },
    })
  } else {
    form.post('/admin/categories', { forceFormData: true })
  }
}
</script>
