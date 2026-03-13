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

        <div>
          <label class="block text-sm font-medium mb-1">Background Image</label>
          <input type="file" accept="image/*" @change="onBackgroundImage" class="w-full text-sm" />
          <p class="text-xs text-muted-foreground mt-1">Displayed behind the page content with a translucent overlay. Max 20MB.</p>
          <p v-if="form.errors.background_image" class="text-destructive text-xs mt-1">{{ form.errors.background_image }}</p>
          <!-- New image preview -->
          <div v-if="backgroundImagePreview" class="mt-3 relative inline-block group">
            <img :src="backgroundImagePreview" class="h-32 rounded object-cover border border-border" />
            <button
              type="button"
              @click="clearBackgroundImage"
              class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <XIcon class="w-3 h-3" />
            </button>
          </div>
          <!-- Existing image -->
          <div v-else-if="page?.background_image && !form.remove_background_image" class="mt-3 relative inline-block group">
            <img :src="page.background_image" class="h-32 rounded object-cover border border-border" />
            <button
              type="button"
              @click="form.remove_background_image = true"
              class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <XIcon class="w-3 h-3" />
            </button>
          </div>
          <p v-if="form.remove_background_image" class="mt-2 text-xs text-destructive">Background image will be removed on save.</p>
        </div>

        <!-- Translations -->
        <div v-if="nonDefaultLanguages.length" class="border border-border rounded-md p-4 space-y-4">
          <button type="button" @click="showTranslations = !showTranslations" class="text-sm font-medium flex items-center gap-2">
            <ChevronDownIcon :class="['w-4 h-4 transition-transform', showTranslations ? 'rotate-0' : '-rotate-90']" />
            Translations
          </button>
          <div v-if="showTranslations" class="space-y-4">
            <div v-for="lang in nonDefaultLanguages" :key="lang.code" class="space-y-2 border-t border-border pt-3">
              <p class="text-xs font-medium text-muted-foreground uppercase">{{ lang.name }} ({{ lang.code }})</p>
              <div>
                <label class="block text-xs text-muted-foreground mb-1">Title</label>
                <input
                  v-model="form.translations[lang.code].title"
                  type="text"
                  class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
                />
              </div>
              <div>
                <label class="block text-xs text-muted-foreground mb-1">Content</label>
                <textarea
                  v-model="form.translations[lang.code].content"
                  rows="6"
                  class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm font-mono"
                />
              </div>
            </div>
          </div>
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
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ChevronDown as ChevronDownIcon, X as XIcon } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  page: { type: Object, default: null },
  pageTranslations: { type: Object, default: () => ({}) },
  languages: { type: Array, default: () => [] },
  defaultLocale: { type: String, default: 'sk' },
})

const showTranslations = ref(!!props.page)

const nonDefaultLanguages = computed(() =>
  props.languages.filter(l => l.code !== props.defaultLocale)
)

const translationsData = {}
for (const lang of nonDefaultLanguages.value) {
  translationsData[lang.code] = {
    title: props.pageTranslations?.[lang.code]?.title ?? '',
    content: props.pageTranslations?.[lang.code]?.content ?? '',
  }
}

const form = useForm({
  title: props.page?.title ?? '',
  slug: props.page?.slug ?? '',
  content: props.page?.content ?? '',
  background_image: null,
  remove_background_image: false,
  is_active: props.page?.is_active ?? true,
  translations: translationsData,
})

const backgroundImagePreview = ref(null)

function onBackgroundImage(e) {
  const file = e.target.files[0]
  if (!file) return
  form.background_image = file
  form.remove_background_image = false
  backgroundImagePreview.value = URL.createObjectURL(file)
}

function clearBackgroundImage() {
  form.background_image = null
  backgroundImagePreview.value = null
}

function submit() {
  if (props.page) {
    form.post(`/admin/pages/${props.page.id}`, { forceFormData: true, headers: { 'X-HTTP-Method-Override': 'PUT' } })
  } else {
    form.post('/admin/pages', { forceFormData: true })
  }
}
</script>
