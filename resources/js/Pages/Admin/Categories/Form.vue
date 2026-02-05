<template>
  <Head :title="category ? 'Edit Category' : 'New Category'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ category ? 'Edit Category' : 'New Category' }}</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Language Tabs -->
        <div v-if="nonDefaultLanguages.length > 0" class="flex gap-1 border-b border-border">
          <button
            type="button"
            @click="activeLocale = defaultLocale"
            class="px-4 py-2 text-sm border-b-2 transition-colors"
            :class="activeLocale === defaultLocale
              ? 'border-primary text-primary font-medium'
              : 'border-transparent text-muted-foreground hover:text-foreground'"
          >
            {{ defaultLanguageName }} (Default)
          </button>
          <button
            v-for="lang in nonDefaultLanguages"
            :key="lang.code"
            type="button"
            @click="activeLocale = lang.code"
            class="px-4 py-2 text-sm border-b-2 transition-colors"
            :class="activeLocale === lang.code
              ? 'border-primary text-primary font-medium'
              : 'border-transparent text-muted-foreground hover:text-foreground'"
          >
            {{ lang.native_name || lang.name }}
          </button>
        </div>

        <!-- Default locale fields -->
        <template v-if="activeLocale === defaultLocale">
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
        </template>

        <!-- Translation locale fields -->
        <template v-else>
          <p class="text-sm text-muted-foreground">
            Translate fields for <strong>{{ activeLanguageName }}</strong>. Leave empty to use the default language value.
          </p>

          <div>
            <label class="block text-sm font-medium mb-1">
              Name
              <span class="text-xs text-muted-foreground ml-1">({{ form.name }})</span>
            </label>
            <input
              v-model="form.translations[activeLocale].name"
              type="text"
              :placeholder="form.name"
              class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">
              Description
              <span class="text-xs text-muted-foreground ml-1">({{ truncate(form.description) }})</span>
            </label>
            <textarea
              v-model="form.translations[activeLocale].description"
              rows="3"
              :placeholder="form.description"
              class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
            />
          </div>
        </template>

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
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  category: { type: Object, default: null },
  categoryTranslations: { type: Object, default: () => ({}) },
  languages: { type: Array, default: () => [] },
  defaultLocale: { type: String, default: 'sk' },
})

const activeLocale = ref(props.defaultLocale)

const defaultLanguageName = computed(() =>
  props.languages.find(l => l.code === props.defaultLocale)?.native_name
    || props.languages.find(l => l.code === props.defaultLocale)?.name
    || props.defaultLocale
)

const nonDefaultLanguages = computed(() =>
  props.languages.filter(l => l.code !== props.defaultLocale)
)

const activeLanguageName = computed(() =>
  props.languages.find(l => l.code === activeLocale.value)?.native_name
    || props.languages.find(l => l.code === activeLocale.value)?.name
    || activeLocale.value
)

// Build translations form data
const translationsData = {}
for (const lang of nonDefaultLanguages.value) {
  translationsData[lang.code] = {
    name: props.categoryTranslations?.[lang.code]?.name ?? '',
    description: props.categoryTranslations?.[lang.code]?.description ?? '',
  }
}

const form = useForm({
  name: props.category?.name ?? '',
  description: props.category?.description ?? '',
  image: null,
  is_active: props.category?.is_active ?? true,
  translations: translationsData,
})

function truncate(text, length = 50) {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

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
