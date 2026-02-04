<template>
  <Head :title="page ? 'Edit Page' : 'New Page'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ page ? 'Edit Page' : 'New Page' }}</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Title</label>
          <input v-model="form.title" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
          <p v-if="form.errors.title" class="text-destructive text-xs mt-1">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Slug</label>
          <input v-model="form.slug" type="text" placeholder="Auto-generated from title" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
          <p v-if="form.errors.slug" class="text-destructive text-xs mt-1">{{ form.errors.slug }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Content</label>
          <textarea v-model="form.content" rows="12" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm font-mono" />
          <p v-if="form.errors.content" class="text-destructive text-xs mt-1">{{ form.errors.content }}</p>
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
            {{ page ? 'Update' : 'Create' }}
          </button>
          <Link href="/admin/pages" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
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
  page: { type: Object, default: null },
})

const form = useForm({
  title: props.page?.title ?? '',
  slug: props.page?.slug ?? '',
  content: props.page?.content ?? '',
  is_active: props.page?.is_active ?? true,
})

function submit() {
  if (props.page) {
    form.put(`/admin/pages/${props.page.id}`)
  } else {
    form.post('/admin/pages')
  }
}
</script>
