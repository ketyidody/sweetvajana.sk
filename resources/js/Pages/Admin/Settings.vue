<template>
  <Head title="Site Settings" />
  <AdminLayout>
    <h1 class="text-2xl font-medium mb-6">Site Settings</h1>

    <form @submit.prevent="submit" class="space-y-8 max-w-2xl">
      <!-- Language Tabs -->
      <div v-if="nonDefaultLanguages.length > 0" class="flex gap-1 border-b border-border">
        <button
          type="button"
          @click="activeLocale = defaultLanguage.code"
          class="px-4 py-2 text-sm border-b-2 transition-colors"
          :class="activeLocale === defaultLanguage.code
            ? 'border-primary text-primary font-medium'
            : 'border-transparent text-muted-foreground hover:text-foreground'"
        >
          {{ defaultLanguage.native_name }} ({{ t('common.default') || 'Default' }})
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
          {{ lang.native_name }}
        </button>
      </div>

      <!-- Default locale fields -->
      <template v-if="activeLocale === defaultLanguage.code">
        <!-- General -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">General</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Site Name</label>
              <input v-model="form.site_name" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Favicon</label>
              <input type="file" accept="image/png,image/x-icon,image/svg+xml,image/jpeg" @change="onFaviconChange" class="w-full text-sm" />
              <p class="text-xs text-muted-foreground mt-1">Recommended: PNG or ICO, max 2MB.</p>
              <img v-if="faviconPreview" :src="faviconPreview" class="mt-2 w-10 h-10 rounded object-contain" />
              <img v-else-if="settings.favicon" :src="settings.favicon" class="mt-2 w-10 h-10 rounded object-contain" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Logo</label>
              <input type="file" accept="image/*" @change="onLogoChange" class="w-full text-sm" />
              <p class="text-xs text-muted-foreground mt-1">Replaces the "Sweet Vajana" text in the header. Max 5MB.</p>
              <img v-if="logoPreview" :src="logoPreview" class="mt-2 h-10 rounded object-contain" />
              <img v-else-if="settings.logo" :src="settings.logo" class="mt-2 h-10 rounded object-contain" />
            </div>
          </div>
        </section>

        <!-- Hero -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">Hero Section</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Title</label>
              <input v-model="form.hero_title" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Subtitle</label>
              <textarea v-model="form.hero_subtitle" rows="2" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Hero Images</label>
              <input type="file" accept="image/*" multiple @change="onHeroImagesChange" class="w-full text-sm" />
              <p class="text-xs text-muted-foreground mt-1">Upload multiple images (max 20MB each). They will rotate as a carousel.</p>
              <div class="flex flex-wrap gap-2 mt-2">
                <div v-for="(url, i) in existingHeroImages" :key="'existing-' + i" class="relative group">
                  <img :src="url" class="w-40 h-24 rounded object-cover" />
                  <button
                    type="button"
                    @click="removeExistingHeroImage(i)"
                    class="absolute top-1 right-1 bg-destructive text-destructive-foreground rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                  >&times;</button>
                </div>
                <div v-for="(preview, i) in heroImagePreviews" :key="'new-' + i" class="relative group">
                  <img :src="preview" class="w-40 h-24 rounded object-cover ring-2 ring-primary" />
                  <button
                    type="button"
                    @click="removeNewHeroImage(i)"
                    class="absolute top-1 right-1 bg-destructive text-destructive-foreground rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                  >&times;</button>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">CTA Button Text</label>
              <input v-model="form.hero_cta_text" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>

        <!-- Contact & Social -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">Contact & Social</h2>
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Phone</label>
                <input v-model="form.phone" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input v-model="form.email" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium mb-1">Instagram URL</label>
                <input v-model="form.instagram_url" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Facebook URL</label>
                <input v-model="form.facebook_url" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">About Text</label>
              <textarea v-model="form.about_text" rows="3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Contact Text</label>
              <textarea v-model="form.contact_text" rows="3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>

        <!-- SEO -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">SEO & Footer</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Meta Description</label>
              <textarea v-model="form.meta_description" rows="2" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Footer Text</label>
              <input v-model="form.footer_text" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>
      </template>

      <!-- Translation locale fields -->
      <template v-else>
        <p class="text-sm text-muted-foreground">
          Translate text fields for <strong>{{ activeLanguageName }}</strong>. Leave empty to use the default language value.
        </p>

        <!-- Translatable: General -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">General</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">
                Site Name
                <span class="text-xs text-muted-foreground ml-1">({{ settings.site_name }})</span>
              </label>
              <input v-model="form.translations[activeLocale].site_name" type="text" :placeholder="settings.site_name" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>

        <!-- Translatable: Hero -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">Hero Section</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">
                Title
                <span class="text-xs text-muted-foreground ml-1">({{ settings.hero_title }})</span>
              </label>
              <input v-model="form.translations[activeLocale].hero_title" type="text" :placeholder="settings.hero_title" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">
                Subtitle
                <span class="text-xs text-muted-foreground ml-1">({{ truncate(settings.hero_subtitle) }})</span>
              </label>
              <textarea v-model="form.translations[activeLocale].hero_subtitle" rows="2" :placeholder="settings.hero_subtitle" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">
                CTA Button Text
                <span class="text-xs text-muted-foreground ml-1">({{ settings.hero_cta_text }})</span>
              </label>
              <input v-model="form.translations[activeLocale].hero_cta_text" type="text" :placeholder="settings.hero_cta_text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>

        <!-- Translatable: About & Contact text -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">Content</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">
                About Text
                <span class="text-xs text-muted-foreground ml-1">({{ truncate(settings.about_text) }})</span>
              </label>
              <textarea v-model="form.translations[activeLocale].about_text" rows="3" :placeholder="settings.about_text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">
                Contact Text
                <span class="text-xs text-muted-foreground ml-1">({{ truncate(settings.contact_text) }})</span>
              </label>
              <textarea v-model="form.translations[activeLocale].contact_text" rows="3" :placeholder="settings.contact_text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>

        <!-- Translatable: SEO & Footer -->
        <section>
          <h2 class="text-lg font-medium mb-4 pb-2 border-b border-border">SEO & Footer</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">
                Meta Description
                <span class="text-xs text-muted-foreground ml-1">({{ truncate(settings.meta_description) }})</span>
              </label>
              <textarea v-model="form.translations[activeLocale].meta_description" rows="2" :placeholder="settings.meta_description" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">
                Footer Text
                <span class="text-xs text-muted-foreground ml-1">({{ settings.footer_text }})</span>
              </label>
              <input v-model="form.translations[activeLocale].footer_text" type="text" :placeholder="settings.footer_text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </div>
        </section>
      </template>

      <div class="pt-4">
        <button
          type="submit"
          :disabled="form.processing"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
        >
          Save Settings
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useTranslation } from '@/composables/useTranslation'

const { t } = useTranslation()

const props = defineProps({
  settings: Object,
  settingTranslations: Object,
  translatableKeys: Array,
  languages: Array,
})

const defaultLanguage = computed(() => props.languages.find(l => l.is_default) || props.languages[0])
const nonDefaultLanguages = computed(() => props.languages.filter(l => !l.is_default))
const activeLocale = ref(defaultLanguage.value.code)
const activeLanguageName = computed(() => props.languages.find(l => l.code === activeLocale.value)?.native_name || activeLocale.value)

const existingHeroImages = ref([...(props.settings.hero_images || [])])
const heroImagePreviews = ref([])
const newHeroFiles = ref([])
const removedHeroImages = ref([])
const faviconPreview = ref(null)
const logoPreview = ref(null)

// Build translations object for each non-default language
function buildTranslationsData() {
  const data = {}
  for (const lang of nonDefaultLanguages.value) {
    data[lang.code] = {}
    for (const key of props.translatableKeys) {
      data[lang.code][key] = props.settingTranslations?.[lang.code]?.[key] ?? ''
    }
  }
  return data
}

const form = useForm({
  site_name: props.settings.site_name ?? '',
  hero_title: props.settings.hero_title ?? '',
  hero_subtitle: props.settings.hero_subtitle ?? '',
  hero_images: [],
  remove_hero_images: [],
  hero_cta_text: props.settings.hero_cta_text ?? '',
  phone: props.settings.phone ?? '',
  email: props.settings.email ?? '',
  instagram_url: props.settings.instagram_url ?? '',
  facebook_url: props.settings.facebook_url ?? '',
  about_text: props.settings.about_text ?? '',
  contact_text: props.settings.contact_text ?? '',
  meta_description: props.settings.meta_description ?? '',
  footer_text: props.settings.footer_text ?? '',
  favicon: null,
  logo: null,
  translations: buildTranslationsData(),
})

function truncate(text, length = 50) {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

function onHeroImagesChange(e) {
  const files = Array.from(e.target.files)
  for (const file of files) {
    newHeroFiles.value.push(file)
    heroImagePreviews.value.push(URL.createObjectURL(file))
  }
}

function removeExistingHeroImage(index) {
  const url = existingHeroImages.value.splice(index, 1)[0]
  removedHeroImages.value.push(url)
}

function removeNewHeroImage(index) {
  newHeroFiles.value.splice(index, 1)
  heroImagePreviews.value.splice(index, 1)
}

function onFaviconChange(e) {
  const file = e.target.files[0]
  form.favicon = file ?? null
  faviconPreview.value = file ? URL.createObjectURL(file) : null
}

function onLogoChange(e) {
  const file = e.target.files[0]
  form.logo = file ?? null
  logoPreview.value = file ? URL.createObjectURL(file) : null
}

function submit() {
  form.hero_images = newHeroFiles.value
  form.remove_hero_images = removedHeroImages.value
  form.post('/admin/settings', { forceFormData: true })
}
</script>
