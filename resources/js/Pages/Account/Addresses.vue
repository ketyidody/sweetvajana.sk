<template>
  <Head title="My Addresses" />
  <div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-medium">My Addresses</h1>
        <Link href="/" class="text-sm text-muted-foreground hover:text-foreground">&larr; Back to shop</Link>
      </div>

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm">
        {{ $page.props.flash.success }}
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <nav class="space-y-1">
          <Link href="/account" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            Overview
          </Link>
          <Link href="/account/orders" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            My Orders
          </Link>
          <Link href="/account/addresses" class="block px-3 py-2 rounded-md text-sm bg-accent text-accent-foreground">
            Addresses
          </Link>
          <Link href="/profile" class="block px-3 py-2 rounded-md text-sm text-muted-foreground hover:bg-accent hover:text-accent-foreground">
            Profile Settings
          </Link>
        </nav>

        <div class="md:col-span-3">
          <form @submit.prevent="submit" class="bg-card rounded-lg border border-border p-6 space-y-4 max-w-xl">
            <div>
              <label class="block text-sm font-medium mb-1">Phone</label>
              <input v-model="form.phone" type="text" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Shipping Address</label>
              <textarea v-model="form.shipping_address" rows="3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Billing Address</label>
              <textarea v-model="form.billing_address" rows="3" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm" />
            </div>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
            >
              Save Addresses
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({ user: Object })

const form = useForm({
  phone: props.user.phone ?? '',
  shipping_address: props.user.shipping_address ?? '',
  billing_address: props.user.billing_address ?? '',
})

function submit() {
  form.post('/account/addresses')
}
</script>
