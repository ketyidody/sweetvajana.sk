<template>
  <Head title="Site Settings" />
  <AdminLayout>
    <h1 class="text-2xl font-medium mb-6">Site Settings</h1>

    <form @submit.prevent="submit" class="space-y-8 max-w-2xl">
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
            <label class="block text-sm font-medium mb-1">Hero Image</label>
            <input type="file" accept="image/*" @change="onHeroImageChange" class="w-full text-sm" />
            <p class="text-xs text-muted-foreground mt-1">Max 20MB.</p>
            <img v-if="heroImagePreview" :src="heroImagePreview" class="mt-2 w-40 h-24 rounded object-cover" />
            <img v-else-if="settings.hero_image" :src="settings.hero_image" class="mt-2 w-40 h-24 rounded object-cover" />
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
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ settings: Object })

const heroImagePreview = ref(null)
const faviconPreview = ref(null)
const logoPreview = ref(null)

const form = useForm({
  site_name: props.settings.site_name ?? '',
  hero_title: props.settings.hero_title ?? '',
  hero_subtitle: props.settings.hero_subtitle ?? '',
  hero_image: null,
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
})

function onHeroImageChange(e) {
  const file = e.target.files[0]
  form.hero_image = file ?? null
  heroImagePreview.value = file ? URL.createObjectURL(file) : null
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
  form.post('/admin/settings', { forceFormData: true })
}
</script>
