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
            <label class="block text-sm font-medium mb-1">Hero Image URL</label>
            <input v-model="form.hero_image" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
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
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ settings: Object })

const form = useForm({
  site_name: props.settings.site_name ?? '',
  hero_title: props.settings.hero_title ?? '',
  hero_subtitle: props.settings.hero_subtitle ?? '',
  hero_image: props.settings.hero_image ?? '',
  hero_cta_text: props.settings.hero_cta_text ?? '',
  phone: props.settings.phone ?? '',
  email: props.settings.email ?? '',
  instagram_url: props.settings.instagram_url ?? '',
  facebook_url: props.settings.facebook_url ?? '',
  about_text: props.settings.about_text ?? '',
  contact_text: props.settings.contact_text ?? '',
  meta_description: props.settings.meta_description ?? '',
  footer_text: props.settings.footer_text ?? '',
})

function submit() {
  form.post('/admin/settings')
}
</script>
