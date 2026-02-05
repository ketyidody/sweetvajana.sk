<template>
  <Head title="Translations" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-medium">Translations</h1>
      <button
        @click="showAddModal = true"
        class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90"
      >
        Add Key
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <select
        v-model="selectedGroup"
        @change="applyFilters"
        class="px-3 py-2 text-sm border border-border rounded-md bg-background"
      >
        <option value="">All groups</option>
        <option v-for="group in groups" :key="group" :value="group">{{ group }}</option>
      </select>
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search keys or values..."
        class="px-3 py-2 text-sm border border-border rounded-md bg-background w-64"
        @keyup.enter="applyFilters"
      />
      <button
        @click="applyFilters"
        class="px-4 py-2 text-sm bg-muted hover:bg-muted/80 rounded-md transition-colors"
      >
        Filter
      </button>
    </div>

    <!-- Translations Table -->
    <div class="bg-card rounded-lg border border-border overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-muted/50">
          <tr>
            <th class="text-left px-4 py-3 font-medium whitespace-nowrap">Group</th>
            <th class="text-left px-4 py-3 font-medium whitespace-nowrap">Key</th>
            <th
              v-for="lang in languages"
              :key="lang.code"
              class="text-left px-4 py-3 font-medium whitespace-nowrap min-w-[200px]"
            >
              {{ lang.name }} ({{ lang.code }})
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <template v-for="(keys, group) in translations" :key="group">
            <tr v-for="(locales, key) in keys" :key="`${group}.${key}`">
              <td class="px-4 py-2 text-muted-foreground whitespace-nowrap">{{ group }}</td>
              <td class="px-4 py-2 font-mono text-xs whitespace-nowrap">{{ key }}</td>
              <td v-for="lang in languages" :key="lang.code" class="px-4 py-2">
                <input
                  :value="locales[lang.code] || ''"
                  @input="trackChange(group, key, lang.code, $event.target.value)"
                  class="w-full px-2 py-1 text-sm border border-border rounded bg-background"
                />
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Save Button -->
    <div v-if="Object.keys(changes).length" class="mt-4 flex items-center gap-3">
      <button
        @click="saveChanges"
        :disabled="saving"
        class="px-6 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
      >
        {{ saving ? 'Saving...' : `Save ${Object.keys(changes).length} change(s)` }}
      </button>
      <button
        @click="changes = {}"
        class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground"
      >
        Discard
      </button>
    </div>

    <!-- Add Key Modal -->
    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showAddModal = false">
      <div class="bg-card rounded-lg border border-border p-6 w-full max-w-lg mx-4">
        <h2 class="text-lg font-medium mb-4">Add Translation Key</h2>
        <form @submit.prevent="addKey" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Group</label>
            <input
              v-model="newKey.group"
              type="text"
              required
              placeholder="e.g. nav, common, product"
              class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background"
            />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Key</label>
            <input
              v-model="newKey.key"
              type="text"
              required
              placeholder="e.g. home, add_to_cart"
              class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background"
            />
          </div>
          <div v-for="lang in languages" :key="lang.code">
            <label class="block text-sm font-medium mb-1">{{ lang.name }} ({{ lang.code }})</label>
            <input
              v-model="newKey.values[lang.code]"
              type="text"
              class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background"
            />
          </div>
          <div class="flex items-center gap-3 pt-2">
            <button
              type="submit"
              :disabled="addingKey"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
            >
              {{ addingKey ? 'Adding...' : 'Add' }}
            </button>
            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  translations: Object,
  groups: Array,
  languages: Array,
  filters: Object,
})

const selectedGroup = ref(props.filters.group || '')
const searchQuery = ref(props.filters.search || '')
const changes = ref({})
const saving = ref(false)
const showAddModal = ref(false)
const addingKey = ref(false)

const newKey = reactive({
  group: '',
  key: '',
  values: {},
})

function trackChange(group, key, locale, value) {
  const changeKey = `${group}.${key}.${locale}`
  changes.value[changeKey] = { group, key, locale, value }
}

function applyFilters() {
  const params = {}
  if (selectedGroup.value) params.group = selectedGroup.value
  if (searchQuery.value) params.search = searchQuery.value
  router.get('/admin/translations', params, { preserveState: true })
}

function saveChanges() {
  saving.value = true
  router.post('/admin/translations', {
    translations: Object.values(changes.value),
  }, {
    preserveState: true,
    onSuccess: () => {
      changes.value = {}
      saving.value = false
    },
    onError: () => {
      saving.value = false
    },
  })
}

function addKey() {
  addingKey.value = true
  const translations = []
  for (const lang of props.languages) {
    translations.push({
      group: newKey.group,
      key: newKey.key,
      locale: lang.code,
      value: newKey.values[lang.code] || '',
    })
  }
  router.post('/admin/translations', { translations }, {
    preserveState: false,
    onSuccess: () => {
      showAddModal.value = false
      addingKey.value = false
      newKey.group = ''
      newKey.key = ''
      newKey.values = {}
    },
    onError: () => {
      addingKey.value = false
    },
  })
}
</script>
