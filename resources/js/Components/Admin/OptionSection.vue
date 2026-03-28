<template>
  <div class="mb-10">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-base font-medium">{{ title }}</h2>
    </div>

    <div class="border border-border rounded-lg overflow-hidden mb-4">
      <!-- Empty state -->
      <div v-if="!items.length" class="px-4 py-6 text-sm text-muted-foreground text-center">
        No options yet.
      </div>

      <!-- Option rows -->
      <div v-for="item in items" :key="item.id" class="border-b border-border last:border-0">
        <!-- Display row -->
        <div v-if="editingId !== item.id" class="flex items-center justify-between px-4 py-3">
          <div>
            <span class="text-sm">{{ item.name }}</span>
            <span v-if="hasPrice" class="ml-3 text-sm text-muted-foreground">€{{ item.price }}</span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="startEdit(item)"
              class="text-xs px-2 py-1 border border-border rounded hover:bg-muted transition-colors"
            >
              Edit
            </button>
            <button
              type="button"
              @click="$emit('delete', item.id)"
              class="text-xs px-2 py-1 border border-destructive/50 text-destructive rounded hover:bg-destructive/10 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>

        <!-- Edit row -->
        <div v-else class="px-4 py-3 bg-muted/30">
          <!-- Language tabs -->
          <div v-if="languages.length" class="flex gap-1 border-b border-border mb-3">
            <button
              type="button"
              @click="editLocale = defaultLocale"
              class="px-3 py-1.5 text-xs border-b-2 transition-colors"
              :class="editLocale === defaultLocale ? 'border-primary text-primary font-medium' : 'border-transparent text-muted-foreground'"
            >
              Default
            </button>
            <button
              v-for="lang in languages"
              :key="lang.code"
              type="button"
              @click="editLocale = lang.code"
              class="px-3 py-1.5 text-xs border-b-2 transition-colors"
              :class="editLocale === lang.code ? 'border-primary text-primary font-medium' : 'border-transparent text-muted-foreground'"
            >
              {{ lang.native_name || lang.name }}
            </button>
          </div>

          <div class="flex items-end gap-3">
            <div class="flex-1">
              <label class="block text-xs text-muted-foreground mb-1">Name</label>
              <input
                v-if="editLocale === defaultLocale"
                v-model="editForm.name"
                type="text"
                class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
              />
              <input
                v-else
                v-model="editForm.translations[editLocale].name"
                type="text"
                :placeholder="editForm.name"
                class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
              />
            </div>
            <div v-if="hasPrice" class="w-28">
              <label class="block text-xs text-muted-foreground mb-1">Price (€)</label>
              <input
                v-model="editForm.price"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
              />
            </div>
            <button
              type="button"
              @click="saveEdit(item.id)"
              class="px-3 py-1.5 text-xs bg-primary text-primary-foreground rounded hover:bg-primary/90 transition-colors"
            >
              Save
            </button>
            <button
              type="button"
              @click="cancelEdit"
              class="px-3 py-1.5 text-xs border border-border rounded hover:bg-muted transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add form -->
    <div class="border border-dashed border-border rounded-lg px-4 py-4">
      <p class="text-xs font-medium text-muted-foreground mb-3">Add new</p>

      <!-- Language tabs for add form -->
      <div v-if="languages.length" class="flex gap-1 border-b border-border mb-3">
        <button
          type="button"
          @click="addLocale = defaultLocale"
          class="px-3 py-1.5 text-xs border-b-2 transition-colors"
          :class="addLocale === defaultLocale ? 'border-primary text-primary font-medium' : 'border-transparent text-muted-foreground'"
        >
          Default
        </button>
        <button
          v-for="lang in languages"
          :key="lang.code"
          type="button"
          @click="addLocale = lang.code"
          class="px-3 py-1.5 text-xs border-b-2 transition-colors"
          :class="addLocale === lang.code ? 'border-primary text-primary font-medium' : 'border-transparent text-muted-foreground'"
        >
          {{ lang.native_name || lang.name }}
        </button>
      </div>

      <div class="flex items-end gap-3">
        <div class="flex-1">
          <label class="block text-xs text-muted-foreground mb-1">Name</label>
          <input
            v-if="addLocale === defaultLocale"
            v-model="addForm.name"
            type="text"
            placeholder="Option name"
            class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
          />
          <input
            v-else
            v-model="addForm.translations[addLocale].name"
            type="text"
            :placeholder="addForm.name || 'Translation'"
            class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
          />
        </div>
        <div v-if="hasPrice" class="w-28">
          <label class="block text-xs text-muted-foreground mb-1">Price (€)</label>
          <input
            v-model="addForm.price"
            type="number"
            step="0.01"
            min="0"
            placeholder="0.00"
            class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
          />
        </div>
        <button
          type="button"
          @click="submitAdd"
          class="px-3 py-1.5 text-xs bg-primary text-primary-foreground rounded hover:bg-primary/90 transition-colors"
        >
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  title: String,
  items: Array,
  languages: { type: Array, default: () => [] },
  defaultLocale: String,
  productId: Number,
  type: String,
  hasPrice: { type: Boolean, default: false },
})

const emit = defineEmits(['add', 'update', 'delete'])

// Add form state
const addLocale = ref(props.defaultLocale)
const addForm = reactive(buildBlankForm())

function buildBlankForm() {
  const t = {}
  for (const lang of props.languages) {
    t[lang.code] = { name: '' }
  }
  return { name: '', price: '', translations: t }
}

function submitAdd() {
  const data = { name: addForm.name, translations: { ...addForm.translations } }
  if (props.hasPrice) data.price = addForm.price
  emit('add', data)
  Object.assign(addForm, buildBlankForm())
  addLocale.value = props.defaultLocale
}

// Edit form state
const editingId = ref(null)
const editLocale = ref(props.defaultLocale)
const editForm = reactive({ name: '', price: '', translations: {} })

function startEdit(item) {
  editingId.value = item.id
  editLocale.value = props.defaultLocale
  editForm.name = item.name
  editForm.price = item.price ?? ''
  editForm.translations = {}
  for (const lang of props.languages) {
    editForm.translations[lang.code] = { name: item.translations?.[lang.code]?.name ?? '' }
  }
}

function saveEdit(id) {
  const data = { name: editForm.name, translations: { ...editForm.translations } }
  if (props.hasPrice) data.price = editForm.price
  emit('update', { id, data })
  cancelEdit()
}

function cancelEdit() {
  editingId.value = null
  editLocale.value = props.defaultLocale
}
</script>
