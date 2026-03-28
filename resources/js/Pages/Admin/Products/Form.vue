<template>
  <Head :title="product ? 'Edit Product' : 'New Product'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ product ? 'Edit Product' : 'New Product' }}</h1>

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
            <label class="block text-sm font-medium mb-1">Category</label>
            <select v-model="form.category_id" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm">
              <option value="">Select category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ '\u00A0'.repeat((cat.depth || 0) * 4) }}{{ cat.depth > 0 ? '\u2514 ' : '' }}{{ cat.name }}
              </option>
            </select>
            <p v-if="form.errors.category_id" class="text-destructive text-xs mt-1">{{ form.errors.category_id }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            <p v-if="form.errors.description" class="text-destructive text-xs mt-1">{{ form.errors.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Price</label>
              <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              <p v-if="form.errors.price" class="text-destructive text-xs mt-1">{{ form.errors.price }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Soonest availability (days)</label>
              <input v-model="form.soonest_availability" type="number" min="1" max="365" placeholder="e.g. 3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
              <p class="text-xs text-muted-foreground mt-1">Leave empty if not applicable.</p>
              <p v-if="form.errors.soonest_availability" class="text-destructive text-xs mt-1">{{ form.errors.soonest_availability }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Main Image</label>
            <input type="file" accept="image/*" @change="form.image = $event.target.files[0]" class="w-full text-sm" />
            <img v-if="product?.image" :src="product.image" class="mt-2 w-20 h-20 rounded object-cover" />
            <p v-if="form.errors.image" class="text-destructive text-xs mt-1">{{ form.errors.image }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Additional Images</label>
            <input
              ref="additionalImagesInput"
              type="file"
              accept="image/*"
              multiple
              @change="onAdditionalImages"
              class="w-full text-sm"
            />
            <p class="text-xs text-muted-foreground mt-1">You can select multiple images (max 10, 20MB each).</p>
            <p v-if="form.errors.additional_images" class="text-destructive text-xs mt-1">{{ form.errors.additional_images }}</p>

            <div v-if="existingImages.length || newImagePreviews.length" class="mt-3 flex flex-wrap gap-3">
              <div v-for="(img, i) in existingImages" :key="'existing-' + i" class="relative group">
                <img :src="img" class="w-20 h-20 rounded object-cover border border-border" />
                <button
                  type="button"
                  @click="removeExistingImage(i)"
                  class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <XIcon class="w-3 h-3" />
                </button>
              </div>
              <div v-for="(preview, i) in newImagePreviews" :key="'new-' + i" class="relative group">
                <img :src="preview" class="w-20 h-20 rounded object-cover border border-border" />
                <button
                  type="button"
                  @click="removeNewImage(i)"
                  class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <XIcon class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>

          <div class="items-center gap-6">
            <div class="flex items-center gap-2">
              <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
              <label for="is_active" class="text-sm">Active</label>
            </div>
            <div class="flex items-center gap-2">
              <input v-model="form.is_featured" type="checkbox" id="is_featured" class="rounded" />
              <label for="is_featured" class="text-sm">Featured</label>
            </div>
            <div class="flex items-center gap-2">
              <input v-model="form.is_orderable_online" type="checkbox" id="is_orderable_online" class="rounded" />
              <label for="is_orderable_online" class="text-sm">Available for online order</label>
            </div>
            <div class="flex tems-center gap-2">
              <input v-model="form.is_available_for_collection" type="checkbox" id="is_available_for_collection" class="rounded" />
              <label for="is_available_for_collection" class="text-sm">Available for immediate collection</label>
            </div>
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
            {{ product ? 'Update' : 'Create' }}
          </button>
          <Link href="/admin/products" class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
            Cancel
          </Link>
        </div>
      </form>

      <!-- Product Sizes (only when editing and is_orderable_online is false) -->
      <div v-if="product && !form.is_orderable_online" class="mt-8 pt-8 border-t border-border">
        <h2 class="text-base font-medium mb-1">Veľkosti produktu (Product Sizes)</h2>
        <p class="text-xs text-muted-foreground mb-4">Assign sizes available for this product. Each size has its own price.</p>

        <!-- Assigned sizes list -->
        <div class="border border-border rounded-lg overflow-hidden mb-4">
          <div v-if="!productSizes.length" class="px-4 py-6 text-sm text-muted-foreground text-center">
            No sizes assigned yet.
          </div>
          <div v-for="ps in productSizes" :key="ps.size_id" class="border-b border-border last:border-0">
            <!-- Display row -->
            <div v-if="editingSizeId !== ps.size_id" class="flex items-center justify-between px-4 py-3">
              <span class="text-sm">{{ ps.name }}</span>
              <div class="flex items-center gap-3">
                <span class="text-sm font-medium">€{{ ps.price }}</span>
                <button
                  type="button"
                  @click="startSizeEdit(ps)"
                  class="text-xs px-2 py-1 border border-border rounded hover:bg-muted transition-colors"
                >Edit price</button>
                <button
                  type="button"
                  @click="removeSize(ps.size_id)"
                  class="text-xs px-2 py-1 border border-destructive/50 text-destructive rounded hover:bg-destructive/10 transition-colors"
                >Remove</button>
              </div>
            </div>
            <!-- Edit price row -->
            <div v-else class="flex items-center gap-3 px-4 py-3 bg-muted/30">
              <span class="text-sm flex-1">{{ ps.name }}</span>
              <input
                v-model="editSizePrice"
                type="number"
                step="0.01"
                min="0"
                class="w-28 px-3 py-1.5 text-sm border border-border rounded bg-background"
              />
              <button
                type="button"
                @click="saveSizePrice(ps.size_id)"
                class="text-xs px-3 py-1.5 bg-primary text-primary-foreground rounded hover:bg-primary/90 transition-colors"
              >Save</button>
              <button
                type="button"
                @click="editingSizeId = null"
                class="text-xs px-3 py-1.5 border border-border rounded hover:bg-muted transition-colors"
              >Cancel</button>
            </div>
          </div>
        </div>

        <!-- Add size form -->
        <div v-if="availableSizes.length" class="border border-dashed border-border rounded-lg px-4 py-4">
          <p class="text-xs font-medium text-muted-foreground mb-3">Add size</p>
          <div class="flex items-end gap-3">
            <div class="flex-1">
              <label class="block text-xs text-muted-foreground mb-1">Size</label>
              <select v-model="newSizeId" class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background">
                <option value="">Select a size</option>
                <option v-for="s in availableSizes" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div class="w-28">
              <label class="block text-xs text-muted-foreground mb-1">Price (€)</label>
              <input
                v-model="newSizePrice"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                class="w-full px-3 py-1.5 text-sm border border-border rounded bg-background"
              />
            </div>
            <button
              type="button"
              @click="addSize"
              :disabled="!newSizeId || newSizePrice === ''"
              class="px-3 py-1.5 text-xs bg-primary text-primary-foreground rounded hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >Add</button>
          </div>
        </div>
        <p v-else-if="!productSizes.length" class="text-xs text-muted-foreground">
          No global sizes available. Add sizes in
          <Link href="/admin/product-options" class="underline">Product Options</Link> first.
        </p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { X as XIcon } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  product: { type: Object, default: null },
  categories: Array,
  productTranslations: { type: Object, default: () => ({}) },
  languages: { type: Array, default: () => [] },
  defaultLocale: { type: String, default: 'sk' },
  allSizes: { type: Array, default: () => [] },
  productSizes: { type: Array, default: () => [] },
})

const activeLocale = ref(props.defaultLocale)
const additionalImagesInput = ref(null)
const existingImages = reactive([...(props.product?.images ?? [])])
const newImages = ref([])
const newImagePreviews = ref([])

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

const translationsData = {}
for (const lang of nonDefaultLanguages.value) {
  translationsData[lang.code] = {
    name: props.productTranslations?.[lang.code]?.name ?? '',
    description: props.productTranslations?.[lang.code]?.description ?? '',
  }
}

const form = useForm({
  category_id: props.product?.category_id ?? '',
  name: props.product?.name ?? '',
  description: props.product?.description ?? '',
  price: props.product?.price ?? '',
  image: null,
  additional_images: [],
  existing_images: [],
  is_active: props.product?.is_active ?? true,
  is_featured: props.product?.is_featured ?? false,
  is_orderable_online: props.product?.is_orderable_online ?? true,
  is_available_for_collection: props.product?.is_available_for_collection ?? false,
  soonest_availability: props.product?.soonest_availability ?? null,
  translations: translationsData,
})

function truncate(text, length = 50) {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

function onAdditionalImages(e) {
  const files = Array.from(e.target.files)
  for (const file of files) {
    newImages.value.push(file)
    newImagePreviews.value.push(URL.createObjectURL(file))
  }
  if (additionalImagesInput.value) {
    additionalImagesInput.value.value = ''
  }
}

function removeExistingImage(index) {
  existingImages.splice(index, 1)
}

function removeNewImage(index) {
  URL.revokeObjectURL(newImagePreviews.value[index])
  newImages.value.splice(index, 1)
  newImagePreviews.value.splice(index, 1)
}

// Sizes management
const editingSizeId = ref(null)
const editSizePrice = ref('')
const newSizeId = ref('')
const newSizePrice = ref('')

const availableSizes = computed(() =>
  props.allSizes.filter(s => !props.productSizes.some(ps => ps.size_id === s.id))
)

function startSizeEdit(ps) {
  editingSizeId.value = ps.size_id
  editSizePrice.value = ps.price
}

function saveSizePrice(sizeId) {
  router.put(`/admin/products/${props.product.id}/sizes/${sizeId}`, { price: editSizePrice.value }, {
    preserveScroll: true,
    onSuccess: () => { editingSizeId.value = null },
  })
}

function removeSize(sizeId) {
  if (confirm('Remove this size from the product?')) {
    router.delete(`/admin/products/${props.product.id}/sizes/${sizeId}`, { preserveScroll: true })
  }
}

function addSize() {
  if (!newSizeId.value || newSizePrice.value === '') return
  router.post(`/admin/products/${props.product.id}/sizes`, {
    size_id: newSizeId.value,
    price: newSizePrice.value,
  }, {
    preserveScroll: true,
    onSuccess: () => { newSizeId.value = ''; newSizePrice.value = '' },
  })
}

function submit() {
  form.additional_images = newImages.value
  form.existing_images = existingImages

  if (props.product) {
    form.post(`/admin/products/${props.product.id}`, {
      forceFormData: true,
      headers: { 'X-HTTP-Method-Override': 'PUT' },
    })
  } else {
    form.post('/admin/products', { forceFormData: true })
  }
}
</script>
