<template>
  <Head :title="t('checkout.title')">
    <meta name="robots" content="noindex, nofollow" />
  </Head>
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

            <!-- Delivery Method -->
            <div class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">{{ t('checkout.delivery_method') }}</h2>
              <p v-if="!deliveryMethods.length" class="text-sm text-muted-foreground">{{ t('checkout.no_delivery_methods') }}</p>
              <div class="space-y-2">
                <label
                  v-for="method in deliveryMethods"
                  :key="method.key"
                  class="flex items-start gap-3 p-3 border rounded-md cursor-pointer transition-colors"
                  :class="form.delivery_method === method.key ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/50'"
                >
                  <input v-model="form.delivery_method" type="radio" name="delivery_method" :value="method.key" class="mt-0.5" />
                  <div class="flex-1">
                    <div class="flex justify-between gap-2">
                      <span class="text-sm font-medium">{{ method.name }}</span>
                      <span class="text-sm">{{ Number(method.price) > 0 ? `€${method.price}` : t('cart.free') }}</span>
                    </div>
                    <p v-if="method.description" class="text-xs text-muted-foreground mt-1">{{ method.description }}</p>
                    <p v-if="method.details" class="text-xs text-muted-foreground mt-1 whitespace-pre-line">{{ method.details }}</p>
                  </div>
                </label>
              </div>
              <p v-if="form.errors.delivery_method" class="text-destructive text-sm mt-1">{{ form.errors.delivery_method }}</p>

              <div v-if="selectedDelivery?.requires_address" class="mt-4">
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
            </div>

            <!-- Invoice Details -->
            <div class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">{{ t('checkout.invoice_details') }}</h2>

              <!-- Company toggle -->
              <label class="flex items-center gap-2 cursor-pointer mb-4">
                <input v-model="invoiceForCompany" type="checkbox" class="rounded" />
                <span class="text-sm">{{ t('checkout.invoice_for_company') }}</span>
              </label>

              <div v-if="invoiceForCompany" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-1">{{ t('checkout.company_name') }}</label>
                  <input
                    v-model="form.company_name"
                    type="text"
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  />
                  <p v-if="form.errors.company_name" class="text-destructive text-sm mt-1">{{ form.errors.company_name }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium mb-1">{{ t('checkout.company_id') }} <span class="text-muted-foreground">({{ t('checkout.optional') }})</span></label>
                    <input
                      v-model="form.company_id"
                      type="text"
                      class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <p v-if="form.errors.company_id" class="text-destructive text-sm mt-1">{{ form.errors.company_id }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium mb-1">{{ t('checkout.vat_number') }} <span class="text-muted-foreground">({{ t('checkout.optional') }})</span></label>
                    <input
                      v-model="form.vat_number"
                      type="text"
                      class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <p v-if="form.errors.vat_number" class="text-destructive text-sm mt-1">{{ form.errors.vat_number }}</p>
                  </div>
                </div>
              </div>

              <!-- Billing address -->
              <div class="mt-4">
                <label v-if="selectedDelivery?.requires_address" class="flex items-center gap-2 cursor-pointer mb-3">
                  <input v-model="billingSameAsShipping" type="checkbox" class="rounded" />
                  <span class="text-sm">{{ t('checkout.billing_same_as_shipping') }}</span>
                </label>
                <div v-if="!selectedDelivery?.requires_address || !billingSameAsShipping">
                  <label class="block text-sm font-medium mb-1">{{ t('checkout.billing_address') }} <span v-if="!selectedDelivery?.requires_address" class="text-muted-foreground">({{ t('checkout.optional') }})</span></label>
                  <textarea
                    v-model="form.billing_address"
                    rows="3"
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                  ></textarea>
                  <p v-if="form.errors.billing_address" class="text-destructive text-sm mt-1">{{ form.errors.billing_address }}</p>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">{{ t('checkout.payment_method') }}</h2>
              <div class="flex items-start gap-3 p-3 border border-primary bg-primary/5 rounded-md">
                <CreditCardIcon class="w-4 h-4 text-muted-foreground mt-0.5" />
                <div class="flex-1">
                  <span class="text-sm font-medium">{{ t('checkout.gopay') }}</span>
                  <p class="text-xs text-muted-foreground mt-1">{{ t('checkout.gopay_desc') }}</p>
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
                :key="item.key"
                class="flex gap-3"
              >
                <img
                  :src="item.image"
                  :alt="item.name"
                  class="w-12 h-12 object-cover rounded-md bg-muted flex-shrink-0"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-sm truncate">{{ item.name }}</p>
                  <p v-for="opt in item.options" :key="opt.group" class="text-xs text-muted-foreground">
                    {{ opt.group }}: {{ opt.values.join(', ') }}
                  </p>
                  <p class="text-xs text-muted-foreground">{{ t('order.qty') }} {{ item.quantity }}</p>
                </div>
                <p class="text-sm flex-shrink-0">€{{ item.subtotal }}</p>
              </div>
            </div>
            <div class="border-t border-border mt-4 pt-4 space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ t('cart.subtotal') }}</span>
                <span>€{{ subtotal }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">{{ selectedDelivery?.name ?? t('cart.shipping') }}</span>
                <span>{{ shippingCost > 0 ? `€${shippingCost.toFixed(2)}` : t('cart.free') }}</span>
              </div>
              <div class="border-t border-border pt-2 mt-2 flex justify-between font-medium text-base">
                <span>{{ t('cart.total') }}</span>
                <span>€{{ total }}</span>
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
import { computed, ref } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft as ArrowLeftIcon, CreditCard as CreditCardIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'
import { useRecaptcha } from '@/composables/useRecaptcha'

const { t } = useTranslation()
const { localizedUrl } = useLocale()
const { execute: executeRecaptcha } = useRecaptcha()

const props = defineProps({
  items: Array,
  subtotal: String,
  deliveryMethods: Array,
  customer: Object,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)

const invoiceForCompany = ref(false)
const billingSameAsShipping = ref(true)

const form = useForm({
  customer_name: props.customer?.name ?? '',
  customer_email: props.customer?.email ?? '',
  customer_phone: props.customer?.phone ?? '',
  delivery_method: props.deliveryMethods[0]?.key ?? '',
  shipping_address: props.customer?.address ?? '',
  notes: '',
  billing_address: '',
  company_name: '',
  company_id: '',
  vat_number: '',
  payment_method: 'gopay',
  recaptcha_token: '',
})

const selectedDelivery = computed(() => props.deliveryMethods.find((m) => m.key === form.delivery_method))
const shippingCost = computed(() => Number(selectedDelivery.value?.price ?? 0))
const total = computed(() => (Number(props.subtotal) + shippingCost.value).toFixed(2))

async function submitOrder() {
  form.recaptcha_token = await executeRecaptcha('checkout')
  form
    .transform((data) => ({
      ...data,
      shipping_address: selectedDelivery.value?.requires_address ? data.shipping_address : '',
      billing_address: selectedDelivery.value?.requires_address && billingSameAsShipping.value ? '' : data.billing_address,
    }))
    .post(localizedUrl('/checkout'))
}
</script>
