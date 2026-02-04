<template>
  <Head :title="product ? 'Edit Product' : 'New Product'" />
  <AdminLayout>
    <div class="max-w-2xl">
      <h1 class="text-2xl font-medium mb-6">{{ product ? 'Edit Product' : 'New Product' }}</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Category</label>
          <select v-model="form.category_id" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm">
            <option value="">Select category</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
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
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input v-model="form.stock" type="number" min="0" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            <p v-if="form.errors.stock" class="text-destructive text-xs mt-1">{{ form.errors.stock }}</p>
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
                <X class="w-3 h-3" />
              </button>
            </div>
            <div v-for="(preview, i) in newImagePreviews" :key="'new-' + i" class="relative group">
              <img :src="preview" class="w-20 h-20 rounded object-cover border border-border" />
              <button
                type="button"
                @click="removeNewImage(i)"
                class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-destructive text-destructive-foreground text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <X class="w-3 h-3" />
              </button>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-2">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
            <label for="is_active" class="text-sm">Active</label>
          </div>
          <div class="flex items-center gap-2">
            <input v-model="form.is_featured" type="checkbox" id="is_featured" class="rounded" />
            <label for="is_featured" class="text-sm">Featured</label>
          </div>
        </div>

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
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  product: { type: Object, default: null },
  categories: Array,
})

const additionalImagesInput = ref(null)
const existingImages = reactive([...(props.product?.images ?? [])])
const newImages = ref([])
const newImagePreviews = ref([])

const form = useForm({
  category_id: props.product?.category_id ?? '',
  name: props.product?.name ?? '',
  description: props.product?.description ?? '',
  price: props.product?.price ?? '',
  stock: props.product?.stock ?? 0,
  image: null,
  additional_images: [],
  existing_images: [],
  is_active: props.product?.is_active ?? true,
  is_featured: props.product?.is_featured ?? false,
})

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
