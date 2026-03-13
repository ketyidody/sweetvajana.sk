<template>
  <Head :title="t('order.confirmed')" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit(localizedUrl('/cart'))" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="max-w-2xl mx-auto">
        <!-- Status Header -->
        <div class="text-center mb-8">
          <template v-if="order.payment_method === 'gopay' && order.payment_status === 'paid'">
            <CheckCircleIcon class="w-16 h-16 mx-auto text-green-600 mb-4" />
            <h1 class="text-3xl md:text-4xl mb-2">{{ t('order.payment_paid') }}</h1>
            <p class="text-muted-foreground">{{ t('order.payment_paid_desc') }}</p>
          </template>
          <template v-else-if="order.payment_method === 'gopay' && order.payment_status === 'failed'">
            <XCircleIcon class="w-16 h-16 mx-auto text-red-600 mb-4" />
            <h1 class="text-3xl md:text-4xl mb-2">{{ t('order.payment_failed') }}</h1>
            <p class="text-muted-foreground">{{ t('order.payment_failed_desc') }}</p>
          </template>
          <template v-else-if="order.payment_method === 'gopay' && order.payment_status === 'pending'">
            <ClockIcon class="w-16 h-16 mx-auto text-yellow-600 mb-4" />
            <h1 class="text-3xl md:text-4xl mb-2">{{ t('order.payment_pending') }}</h1>
            <p class="text-muted-foreground">{{ t('order.payment_pending_desc') }}</p>
          </template>
          <template v-else>
            <CheckCircleIcon class="w-16 h-16 mx-auto text-green-600 mb-4" />
            <h1 class="text-3xl md:text-4xl mb-2">{{ t('order.confirmed') }}</h1>
          </template>
          <p class="text-muted-foreground mt-2">
            {{ t('order.thank_you') }}
            <span class="font-medium text-foreground">{{ order.order_number }}</span>
          </p>
        </div>

        <!-- Order Details -->
        <div class="bg-card rounded-lg border border-border p-6 mb-6">
          <h2 class="text-lg font-medium mb-4">{{ t('order.details') }}</h2>
          <div class="space-y-3">
            <div
              v-for="item in order.items"
              :key="item.product_name"
              class="flex justify-between items-center text-sm"
            >
              <div>
                <p>{{ item.product_name }}</p>
                <p class="text-muted-foreground">{{ t('order.qty') }} {{ item.quantity }} × €{{ item.price }}</p>
              </div>
              <p>€{{ item.subtotal }}</p>
            </div>
          </div>
          <div class="border-t border-border mt-4 pt-4 space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-muted-foreground">{{ t('cart.subtotal') }}</span>
              <span>€{{ order.subtotal }}</span>
            </div>
            <div v-if="Number(order.shipping) > 0" class="flex justify-between">
              <span class="text-muted-foreground">{{ t('cart.shipping') }}</span>
              <span>€{{ order.shipping }}</span>
            </div>
            <div v-else class="flex justify-between">
              <span class="text-muted-foreground">{{ t('cart.shipping') }}</span>
              <span>{{ t('cart.free') }}</span>
            </div>
            <div class="border-t border-border pt-2 mt-2 flex justify-between font-medium text-base">
              <span>{{ t('cart.total') }}</span>
              <span>€{{ order.total }}</span>
            </div>
          </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-card rounded-lg border border-border p-6 mb-8">
          <h2 class="text-lg font-medium mb-4">{{ t('order.delivery_info') }}</h2>
          <div class="space-y-2 text-sm">
            <p><span class="text-muted-foreground">{{ t('order.name') }}</span> {{ order.customer_name }}</p>
            <p><span class="text-muted-foreground">{{ t('order.email') }}</span> {{ order.customer_email }}</p>
            <p><span class="text-muted-foreground">{{ t('order.phone') }}</span> {{ order.customer_phone }}</p>
            <p><span class="text-muted-foreground">{{ t('order.address') }}</span> {{ order.shipping_address }}</p>
            <p v-if="order.notes"><span class="text-muted-foreground">{{ t('order.notes') }}</span> {{ order.notes }}</p>
            <p>
              <span class="text-muted-foreground">{{ t('order.payment_method') }}</span>
              {{ t('order.method_gopay') }}
            </p>
            <p v-if="order.payment_method === 'gopay'">
              <span class="text-muted-foreground">{{ t('order.payment_status') }}</span>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-1"
                :class="{
                  'bg-green-100 text-green-800': order.payment_status === 'paid',
                  'bg-yellow-100 text-yellow-800': order.payment_status === 'pending',
                  'bg-red-100 text-red-800': order.payment_status === 'failed',
                }"
              >
                {{ order.payment_status === 'paid' ? t('order.status_paid') : order.payment_status === 'failed' ? t('order.status_failed') : t('order.status_pending') }}
              </span>
            </p>
          </div>
        </div>

        <div class="text-center">
          <Link
            :href="localizedUrl('/products')"
            class="inline-flex items-center justify-center px-6 py-2.5 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors text-sm font-medium"
          >
            {{ t('common.continue_shopping') }}
          </Link>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { CheckCircle as CheckCircleIcon, XCircle as XCircleIcon, Clock as ClockIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()

defineProps({
  order: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)
</script>
