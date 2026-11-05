<template>
  <Head title="Product Options" />
  <AdminLayout>
    <div class="max-w-3xl">
      <h1 class="text-2xl font-medium mb-2">Product Options</h1>
      <p class="text-sm text-muted-foreground mb-8">
        Corpus and cream flavor are shown to customers for all products that are not available for online order.
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

      <!-- Option groups -->
      <div class="border-t border-border pt-8">
        <h2 class="text-lg font-medium mb-2">Option groups</h2>
        <p class="text-sm text-muted-foreground mb-6">
          Groups of choices (e.g. praline flavours) that can be assigned to online products.
          Assign a group and set how many options the customer may pick on the product edit page.
        </p>

        <OptionSection
          title="Groups"
          :items="optionGroups"
          :languages="nonDefaultLanguages"
          :default-locale="defaultLocale"
          type="option-group"
          @add="addGroup"
          @update="updateGroup"
          @delete="deleteGroup"
        />

        <OptionSection
          v-for="group in optionGroups"
          :key="group.id"
          :title="`${group.name} — options`"
          :items="group.values"
          :languages="nonDefaultLanguages"
          :default-locale="defaultLocale"
          type="option-group-value"
          @add="data => addValue(group.id, data)"
          @update="updateValue"
          @delete="deleteValue"
        />
      </div>
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
  optionGroups: Array,
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

// Option groups
function addGroup(data) {
  useForm(data).post('/admin/product-options/groups', { preserveScroll: true })
}
function updateGroup({ id, data }) {
  useForm(data).put(`/admin/product-options/groups/${id}`, { preserveScroll: true })
}
function deleteGroup(id) {
  if (confirm('Delete this option group and all its options? It will be removed from all products.')) {
    router.delete(`/admin/product-options/groups/${id}`, { preserveScroll: true })
  }
}
function addValue(groupId, data) {
  useForm(data).post(`/admin/product-options/groups/${groupId}/values`, { preserveScroll: true })
}
function updateValue({ id, data }) {
  useForm(data).put(`/admin/product-options/group-values/${id}`, { preserveScroll: true })
}
function deleteValue(id) {
  if (confirm('Delete this option?')) {
    router.delete(`/admin/product-options/group-values/${id}`, { preserveScroll: true })
  }
}
</script>
