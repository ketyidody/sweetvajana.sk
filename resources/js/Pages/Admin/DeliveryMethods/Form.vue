<template>
  <Head :title="`Edit ${method.name}`" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-1">Edit Delivery Method</h1>
      <p class="text-sm text-muted-foreground font-mono mb-6">{{ method.key }}</p>

      <form @submit.prevent="submit" class="space-y-6">
        <section class="space-y-4">
          <div v-if="nonDefaultLanguages.length > 0" class="flex gap-1 border-b border-border">
            <button
              v-for="lang in [defaultLanguage, ...nonDefaultLanguages]"
              :key="lang.code"
              type="button"
              @click="activeLocale = lang.code"
              class="px-4 py-2 text-sm border-b-2 transition-colors"
              :class="activeLocale === lang.code
                ? 'border-primary text-primary font-medium'
                : 'border-transparent text-muted-foreground hover:text-foreground'"
            >
              {{ lang.native_name || lang.name }}<template v-if="lang.code === defaultLocale"> (Default)</template>
            </button>
          </div>

          <template v-if="activeLocale === defaultLocale">
            <div>
              <label class="block text-sm font-medium mb-1">Name</label>
              <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              <p class="text-xs text-muted-foreground mt-1">Shown to customers at checkout and on their order.</p>
              <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Description <span class="text-muted-foreground font-normal">(optional)</span></label>
              <textarea v-model="form.description" rows="2" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              <p v-if="form.errors.description" class="text-destructive text-xs mt-1">{{ form.errors.description }}</p>
            </div>
          </template>

          <template v-else>
            <p class="text-sm text-muted-foreground">
              Translate fields for <strong>{{ activeLanguageName }}</strong>. Leave empty to use the default language value.
            </p>
            <div>
              <label class="block text-sm font-medium mb-1">Name</label>
              <input v-model="form.translations[activeLocale].name" type="text" :placeholder="form.name" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Description</label>
              <textarea v-model="form.translations[activeLocale].description" rows="2" :placeholder="form.description" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
          </template>
        </section>

        <section class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Price (€)</label>
            <input v-model="form.price" type="number" min="0" step="0.01" class="w-40 px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            <p class="text-xs text-muted-foreground mt-1">Added to the order total. Use 0 for free.</p>
            <p v-if="form.errors.price" class="text-destructive text-xs mt-1">{{ form.errors.price }}</p>
          </div>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="form.is_active" type="checkbox" class="rounded" />
            <span class="text-sm">Enabled — offered to customers at checkout</span>
          </label>
          <p class="text-xs text-muted-foreground">
            {{ method.requires_address ? 'Customers must enter a delivery address.' : 'Customers do not need to enter a delivery address.' }}
          </p>
        </section>

        <section v-if="settingsFields.length" class="space-y-4">
          <h2 class="text-lg font-medium pb-2 border-b border-border">Settings</h2>
          <div v-for="field in settingsFields" :key="field.key">
            <label class="block text-sm font-medium mb-1">
              {{ field.label }}
              <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <textarea
              v-if="field.type === 'textarea'"
              v-model="form.settings[field.key]"
              rows="3"
              class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
            />
            <input
              v-else
              v-model="form.settings[field.key]"
              :type="field.type === 'password' ? 'password' : 'text'"
              :placeholder="field.type === 'password' && field.has_value ? '•••••••• (saved — leave empty to keep)' : ''"
              autocomplete="off"
              class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm"
            />
            <p v-if="field.help" class="text-xs text-muted-foreground mt-1">{{ field.help }}</p>
            <p v-if="form.errors[`settings.${field.key}`]" class="text-destructive text-xs mt-1">{{ form.errors[`settings.${field.key}`] }}</p>
          </div>
        </section>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50">
            Save
          </button>
          <Link href="/admin/delivery-methods" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">Cancel</Link>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  method: Object,
  methodTranslations: { type: Object, default: () => ({}) },
  settingsFields: { type: Array, default: () => [] },
  languages: { type: Array, default: () => [] },
  defaultLocale: { type: String, default: 'sk' },
})

const activeLocale = ref(props.defaultLocale)

const defaultLanguage = computed(() =>
  props.languages.find((l) => l.code === props.defaultLocale) ?? { code: props.defaultLocale, name: props.defaultLocale }
)
const nonDefaultLanguages = computed(() => props.languages.filter((l) => l.code !== props.defaultLocale))
const activeLanguageName = computed(() => {
  const lang = props.languages.find((l) => l.code === activeLocale.value)
  return lang?.native_name || lang?.name || activeLocale.value
})

const translations = {}
for (const lang of nonDefaultLanguages.value) {
  translations[lang.code] = {
    name: props.methodTranslations?.[lang.code]?.name ?? '',
    description: props.methodTranslations?.[lang.code]?.description ?? '',
  }
}

const form = useForm({
  name: props.method.name,
  description: props.method.description ?? '',
  price: props.method.price,
  is_active: props.method.is_active,
  translations,
  settings: Object.fromEntries(props.settingsFields.map((f) => [f.key, f.value ?? ''])),
})

function submit() {
  form.put(`/admin/delivery-methods/${props.method.id}`)
}
</script>
