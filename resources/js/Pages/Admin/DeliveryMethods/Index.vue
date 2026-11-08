<template>
  <Head title="Delivery" />
  <AdminLayout>
    <div class="mb-6">
      <h1 class="text-2xl font-medium">Delivery</h1>
      <p class="text-sm text-muted-foreground mt-1">Delivery methods offered at checkout, in this order.</p>
    </div>

    <div class="bg-card rounded-lg border border-border">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-border text-left">
              <th class="p-3 w-16"></th>
              <th class="p-3 font-medium text-muted-foreground">Name</th>
              <th class="p-3 font-medium text-muted-foreground">Price</th>
              <th class="p-3 font-medium text-muted-foreground">Address</th>
              <th class="p-3 font-medium text-muted-foreground">Status</th>
              <th class="p-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(method, index) in methods" :key="method.id" class="border-b border-border last:border-0">
              <td class="p-3">
                <div class="flex flex-col text-muted-foreground">
                  <button type="button" :disabled="index === 0" class="hover:text-foreground disabled:opacity-30" @click="move(index, -1)">
                    <ChevronUpIcon class="w-4 h-4" />
                  </button>
                  <button type="button" :disabled="index === methods.length - 1" class="hover:text-foreground disabled:opacity-30" @click="move(index, 1)">
                    <ChevronDownIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
              <td class="p-3">
                <div class="font-medium">{{ method.name }}</div>
                <div class="text-xs text-muted-foreground font-mono">{{ method.key }}</div>
              </td>
              <td class="p-3">{{ Number(method.price) > 0 ? `€${method.price}` : 'Free' }}</td>
              <td class="p-3 text-muted-foreground">{{ method.requires_address ? 'Required' : 'Not needed' }}</td>
              <td class="p-3">
                <button
                  type="button"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="method.is_active ? 'bg-green-100 text-green-800' : 'bg-muted text-muted-foreground'"
                  @click="toggle(method)"
                >
                  {{ method.is_active ? 'Enabled' : 'Disabled' }}
                </button>
                <p v-if="!method.is_configured" class="text-xs text-destructive mt-1">Missing required settings</p>
              </td>
              <td class="p-3">
                <Link :href="`/admin/delivery-methods/${method.id}/edit`" class="text-muted-foreground hover:text-foreground">
                  <PencilIcon class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="!methods.length">
              <td colspan="6" class="p-3 text-center text-muted-foreground">No delivery methods are registered.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ChevronDown as ChevronDownIcon, ChevronUp as ChevronUpIcon, Pencil as PencilIcon } from 'lucide-vue-next'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ methods: Array })

function toggle(method) {
  router.patch(`/admin/delivery-methods/${method.id}/toggle`, {}, { preserveScroll: true })
}

function move(index, offset) {
  const ids = props.methods.map((m) => m.id)
  ;[ids[index], ids[index + offset]] = [ids[index + offset], ids[index]]
  router.post('/admin/delivery-methods/reorder', { ids }, { preserveScroll: true })
}
</script>
