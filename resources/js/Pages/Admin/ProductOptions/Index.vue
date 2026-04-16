<template>
  <Head title="Product Options" />
  <AdminLayout>
    <div class="max-w-3xl">
      <h1 class="text-2xl font-medium mb-2">Product Options</h1>
      <p class="text-sm text-muted-foreground mb-8">
        These options are shown to customers for all products that are not available for online order.
      </p>

      <!-- Corpus -->
      <OptionSection
        title="Korpus (Corpus)"
        :items="corpuses"
        :languages="nonDefaultLanguages"
        :default-locale="defaultLocale"
        type="corpus"
        @add="addCorpus"
        @update="updateCorpus"
        @delete="deleteCorpus"
      />

      <!-- Cream Flavor -->
      <OptionSection
        title="Príchuť krému (Cream Flavor)"
        :items="creamFlavors"
        :languages="nonDefaultLanguages"
        :default-locale="defaultLocale"
        type="cream-flavor"
        @add="addCreamFlavor"
        @update="updateCreamFlavor"
        @delete="deleteCreamFlavor"
      />

    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import OptionSection from '@/Components/Admin/OptionSection.vue'

const props = defineProps({
  languages: Array,
  defaultLocale: String,
  corpuses: Array,
  creamFlavors: Array,
})

const nonDefaultLanguages = computed(() => props.languages.filter(l => l.code !== props.defaultLocale))

// Corpus
function addCorpus(data) {
  useForm(data).post('/admin/product-options/corpuses', { preserveScroll: true })
}
function updateCorpus({ id, data }) {
  useForm(data).put(`/admin/product-options/corpuses/${id}`, { preserveScroll: true })
}
function deleteCorpus(id) {
  if (confirm('Delete this corpus?')) {
    router.delete(`/admin/product-options/corpuses/${id}`, { preserveScroll: true })
  }
}

// Cream Flavor
function addCreamFlavor(data) {
  useForm(data).post('/admin/product-options/cream-flavors', { preserveScroll: true })
}
function updateCreamFlavor({ id, data }) {
  useForm(data).put(`/admin/product-options/cream-flavors/${id}`, { preserveScroll: true })
}
function deleteCreamFlavor(id) {
  if (confirm('Delete this cream flavor?')) {
    router.delete(`/admin/product-options/cream-flavors/${id}`, { preserveScroll: true })
  }
}

</script>
