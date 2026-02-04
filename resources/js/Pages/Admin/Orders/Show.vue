<template>
  <Head :title="`Order ${order.order_number}`" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-6">
      <div>
        <Link href="/admin/orders" class="text-sm text-muted-foreground hover:text-foreground mb-2 inline-block">&larr; Back to orders</Link>
        <h1 class="text-2xl font-medium">Order {{ order.order_number }}</h1>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Items -->
        <div class="bg-card rounded-lg border border-border">
          <div class="p-4 border-b border-border">
            <h2 class="font-medium">Items</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-border text-left">
                  <th class="p-3 font-medium text-muted-foreground">Product</th>
                  <th class="p-3 font-medium text-muted-foreground">Price</th>
                  <th class="p-3 font-medium text-muted-foreground">Qty</th>
                  <th class="p-3 font-medium text-muted-foreground text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in order.items" :key="item.id" class="border-b border-border last:border-0">
                  <td class="p-3">{{ item.product_name }}</td>
                  <td class="p-3">${{ item.price }}</td>
                  <td class="p-3">{{ item.quantity }}</td>
                  <td class="p-3 text-right">${{ item.subtotal }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t border-border">
                  <td colspan="3" class="p-3 text-right text-muted-foreground">Subtotal</td>
                  <td class="p-3 text-right">${{ order.subtotal }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="p-3 text-right text-muted-foreground">Tax</td>
                  <td class="p-3 text-right">${{ order.tax }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="p-3 text-right text-muted-foreground">Shipping</td>
                  <td class="p-3 text-right">${{ order.shipping }}</td>
                </tr>
                <tr class="font-medium">
                  <td colspan="3" class="p-3 text-right">Total</td>
                  <td class="p-3 text-right">${{ order.total }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Status update -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Update Status</h2>
          <form @submit.prevent="updateStatus">
            <div class="space-y-3">
              <div>
                <label class="block text-sm text-muted-foreground mb-1">Order Status</label>
                <select v-model="form.status" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm">
                  <option value="pending">Pending</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label class="block text-sm text-muted-foreground mb-1">Payment Status</label>
                <select v-model="form.payment_status" class="w-full px-3 py-2 border border-border rounded-md bg-input-background text-sm">
                  <option value="pending">Pending</option>
                  <option value="paid">Paid</option>
                  <option value="failed">Failed</option>
                  <option value="refunded">Refunded</option>
                </select>
              </div>
              <button type="submit" :disabled="form.processing" class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50">
                Update
              </button>
            </div>
          </form>
        </div>

        <!-- Customer info -->
        <div class="bg-card rounded-lg border border-border p-4">
          <h2 class="font-medium mb-3">Customer</h2>
          <dl class="text-sm space-y-2">
            <div>
              <dt class="text-muted-foreground">Name</dt>
              <dd>{{ order.customer_name }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Email</dt>
              <dd>{{ order.customer_email }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Phone</dt>
              <dd>{{ order.customer_phone }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Shipping Address</dt>
              <dd class="whitespace-pre-line">{{ order.shipping_address }}</dd>
            </div>
            <div v-if="order.billing_address">
              <dt class="text-muted-foreground">Billing Address</dt>
              <dd class="whitespace-pre-line">{{ order.billing_address }}</dd>
            </div>
            <div v-if="order.notes">
              <dt class="text-muted-foreground">Notes</dt>
              <dd>{{ order.notes }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ order: Object })

const form = useForm({
  status: props.order.status,
  payment_status: props.order.payment_status,
})

function updateStatus() {
  form.patch(`/admin/orders/${props.order.id}`)
}
</script>
