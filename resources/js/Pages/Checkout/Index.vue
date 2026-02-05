<template>
  <Head :title="t('checkout.title')" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit(localizedUrl('/cart'))" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <Link
        :href="localizedUrl('/cart')"
        class="inline-flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors mb-6"
      >
        <ArrowLeftIcon class="w-4 h-4" />
        {{ t('checkout.back_to_cart') }}
      </Link>

      <h1 class="text-3xl md:text-4xl mb-8">{{ t('checkout.title') }}</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Customer Info Form -->
        <div class="lg:col-span-2">
          <form @submit.prevent="submitOrder" class="space-y-6">
            <div class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">{{ t('checkout.customer_info') }}</h2>
              <div class="space-y-4">
                <div>
                  <label for="customer_name" class="block text-sm font-medium mb-1">{{ t('checkout.full_name') }}</label>
                  <input
                    id="customer_name"
                    v-model="form.customer_name"
                    type="text"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  />
                  <p v-if="form.errors.customer_name" class="text-destructive text-sm mt-1">{{ form.errors.customer_name }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label for="customer_email" class="block text-sm font-medium mb-1">{{ t('checkout.email') }}</label>
                    <input
                      id="customer_email"
                      v-model="form.customer_email"
                      type="email"
                      required
                      class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <p v-if="form.errors.customer_email" class="text-destructive text-sm mt-1">{{ form.errors.customer_email }}</p>
                  </div>
                  <div>
                    <label for="customer_phone" class="block text-sm font-medium mb-1">{{ t('checkout.phone') }}</label>
                    <input
                      id="customer_phone"
                      v-model="form.customer_phone"
                      type="tel"
                      required
                      class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <p v-if="form.errors.customer_phone" class="text-destructive text-sm mt-1">{{ form.errors.customer_phone }}</p>
                  </div>
                </div>
                <div>
                  <label for="shipping_address" class="block text-sm font-medium mb-1">{{ t('checkout.shipping_address') }}</label>
                  <textarea
                    id="shipping_address"
                    v-model="form.shipping_address"
                    rows="3"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                  ></textarea>
                  <p v-if="form.errors.shipping_address" class="text-destructive text-sm mt-1">{{ form.errors.shipping_address }}</p>
                </div>
                <div>
                  <label for="notes" class="block text-sm font-medium mb-1">{{ t('checkout.order_notes') }} <span class="text-muted-foreground">({{ t('checkout.optional') }})</span></label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="2"
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                  ></textarea>
                </div>
              </div>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full px-4 py-3 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ form.processing ? t('checkout.placing_order') : t('checkout.place_order') }}
            </button>
          </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-card rounded-lg border border-border p-6 sticky top-24">
            <h2 class="text-lg font-medium mb-4">{{ t('cart.order_summary') }}</h2>
            <div class="space-y-3">
              <div
                v-for="item in items"
                :key="item.product_id"
                class="flex gap-3"
              >
                <img
                  :src="item.image"
                  :alt="item.name"
                  class="w-12 h-12 object-cover rounded-md bg-muted flex-shrink-0"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-sm truncate">{{ item.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ t('order.qty') }} {{ item.quantity }}</p>
                </div>
                <p class="text-sm flex-shrink-0">${{ item.subtotal }}</p>
              </div>
            </div>
            <div class="border-t border-border mt-4 pt-4 space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ t('cart.subtotal') }}</span>
                <span>${{ subtotal }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ t('cart.shipping') }}</span>
                <span>{{ t('cart.free') }}</span>
              </div>
              <div class="border-t border-border pt-2 mt-2 flex justify-between font-medium text-base">
                <span>{{ t('cart.total') }}</span>
                <span>${{ total }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft as ArrowLeftIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()

const props = defineProps({
  items: Array,
  subtotal: String,
  total: String,
  customer: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)

const form = useForm({
  customer_name: props.customer?.name ?? '',
  customer_email: props.customer?.email ?? '',
  customer_phone: props.customer?.phone ?? '',
  shipping_address: props.customer?.address ?? '',
  notes: '',
})

function submitOrder() {
  form.post(localizedUrl('/checkout'))
}
</script>
