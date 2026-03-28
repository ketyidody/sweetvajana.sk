<template>
  <Head :title="`Special Order #${specialOrder.id}`" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <div>
        <Link href="/admin/special-orders" class="text-sm text-muted-foreground hover:text-foreground mb-2 inline-block">&larr; Back to special orders</Link>
        <h1 class="text-2xl font-medium">Special Order #{{ specialOrder.id }}</h1>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Product -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Product</h2>
          <dl class="text-sm space-y-2">
            <div>
              <dt class="text-muted-foreground">Product Name</dt>
              <dd>{{ specialOrder.product_name }}</dd>
            </div>
            <div v-if="specialOrder.message">
              <dt class="text-muted-foreground">Message / Requirements</dt>
              <dd class="whitespace-pre-line">{{ specialOrder.message }}</dd>
            </div>
            <div v-if="specialOrder.choices?.size">
              <dt class="text-muted-foreground">Veľkosť (Size)</dt>
              <dd class="flex items-center justify-between">
                <span>{{ specialOrder.choices.size.name }}</span>
                <span class="text-muted-foreground ml-4">€{{ specialOrder.choices.size.price }}</span>
              </dd>
            </div>
            <div v-if="specialOrder.choices?.corpus">
              <dt class="text-muted-foreground">Korpus (Corpus)</dt>
              <dd>{{ specialOrder.choices.corpus.name }}</dd>
            </div>
            <div v-if="specialOrder.choices?.cream_flavor">
              <dt class="text-muted-foreground">Príchuť krému (Cream Flavor)</dt>
              <dd>{{ specialOrder.choices.cream_flavor.name }}</dd>
            </div>
            <div v-if="specialOrder.choices?.additions?.length">
              <dt class="text-muted-foreground">Doplnky (Additions)</dt>
              <dd>
                <div v-for="addition in specialOrder.choices.additions" :key="addition.id" class="flex items-center justify-between">
                  <span>{{ addition.name }}</span>
                  <span class="text-muted-foreground ml-4">+€{{ addition.price }}</span>
                </div>
              </dd>
            </div>
          </dl>
        </div>

        <!-- Customer -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Customer</h2>
          <dl class="text-sm space-y-2">
            <div>
              <dt class="text-muted-foreground">Name</dt>
              <dd>{{ specialOrder.customer_name }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Email</dt>
              <dd>{{ specialOrder.customer_email }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Phone</dt>
              <dd>{{ specialOrder.customer_phone }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Status update -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Update Status</h2>
          <form @submit.prevent="updateStatus">
            <div class="space-y-3">
              <div>
                <label class="block text-sm text-muted-foreground mb-1">Status</label>
                <select v-model="form.status" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm">
                  <option value="new">New</option>
                  <option value="contacted">Contacted</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <button type="submit" :disabled="form.processing" class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50">
                Update
              </button>
            </div>
          </form>
        </div>

        <!-- Delete -->
        <div class="bg-card rounded-lg border border-border p-4">
          <button
            @click="deleteOrder"
            class="w-full px-4 py-2 bg-destructive text-destructive-foreground rounded-md text-sm hover:bg-destructive/90"
          >
            Delete Special Order
          </button>
        </div>

        <!-- Meta -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Info</h2>
          <dl class="text-sm space-y-2">
            <div>
              <dt class="text-muted-foreground">Created</dt>
              <dd>{{ new Date(specialOrder.created_at).toLocaleString() }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ specialOrder: Object })

const form = useForm({
  status: props.specialOrder.status,
})

function updateStatus() {
  form.patch(`/admin/special-orders/${props.specialOrder.id}`)
}

function deleteOrder() {
  if (confirm('Are you sure you want to delete this special order?')) {
    router.delete(`/admin/special-orders/${props.specialOrder.id}`)
  }
}
</script>
