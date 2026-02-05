<template>
  <Head :title="language ? 'Edit Language' : 'New Language'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ language ? 'Edit Language' : 'New Language' }}</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Code</label>
          <input
            v-model="form.code"
            type="text"
            maxlength="5"
            placeholder="e.g. en, sk, de"
            class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.code" class="text-destructive text-xs mt-1">{{ form.errors.code }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="e.g. English, Slovak"
            class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Native Name</label>
          <input
            v-model="form.native_name"
            type="text"
            placeholder="e.g. English, Slovensky"
            class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.native_name" class="text-destructive text-xs mt-1">{{ form.errors.native_name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Sort Order</label>
          <input
            v-model.number="form.sort_order"
            type="number"
            min="0"
            class="w-32 px-3 py-2 border border-border rounded-md bg-input-background text-sm"
          />
          <p v-if="form.errors.sort_order" class="text-destructive text-xs mt-1">{{ form.errors.sort_order }}</p>
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-2">
            <input v-model="form.is_default" type="checkbox" id="is_default" class="rounded" />
            <label for="is_default" class="text-sm">Default language</label>
          </div>
          <div class="flex items-center gap-2">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
            <label for="is_active" class="text-sm">Active</label>
          </div>
        </div>

        <div class="flex items-center gap-3 pt-4">
          <button
            type="submit"
            :disabled="form.processing"
            class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
          >
            {{ language ? 'Update' : 'Create' }}
          </button>
          <Link href="/admin/languages" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
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
  language: { type: Object, default: null },
})

const form = useForm({
  code: props.language?.code ?? '',
  name: props.language?.name ?? '',
  native_name: props.language?.native_name ?? '',
  is_default: props.language?.is_default ?? false,
  is_active: props.language?.is_active ?? true,
  sort_order: props.language?.sort_order ?? 0,
})

function submit() {
  if (props.language) {
    form.put(`/admin/languages/${props.language.id}`)
  } else {
    form.post('/admin/languages')
  }
}
</script>
