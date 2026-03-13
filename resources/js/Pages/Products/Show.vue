<template>
  <Head :title="product.name">
    <meta head-key="description" name="description" :content="metaDescription" />
    <meta head-key="og:title" property="og:title" :content="product.name" />
    <meta head-key="og:description" property="og:description" :content="metaDescription" />
    <meta head-key="og:type" property="og:type" content="product" />
    <meta v-if="product.image_url" head-key="og:image" property="og:image" :content="product.image_url" />
    <meta v-if="product.image_url" head-key="twitter:image" name="twitter:image" :content="product.image_url" />
  </Head>
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit(localizedUrl('/cart'))" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back link -->
      <Link
        :href="localizedUrl('/products')"
        class="inline-flex items-center gap-1 text-muted-foreground hover:text-foreground transition-colors mb-6"
      >
        <ArrowLeftIcon class="w-4 h-4" />
        {{ t('product.back_to_products') }}
      </Link>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Image Gallery -->
        <div>
          <div class="aspect-square overflow-hidden rounded-lg bg-muted border border-border">
            <img
              :src="selectedImage"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>
          <div v-if="product.images.length > 1" class="grid grid-cols-4 gap-3 mt-3">
            <button
              v-for="(image, index) in product.images"
              :key="index"
              class="aspect-square overflow-hidden rounded-md bg-muted border-2 transition-colors"
              :class="selectedImage === image ? 'border-primary' : 'border-border hover:border-primary/50'"
              @click="selectedImage = image"
            >
              <img
                :src="image"
                :alt="`${product.name} - Image ${index + 1}`"
                class="w-full h-full object-cover"
              />
            </button>
          </div>
        </div>

        <!-- Product Info -->
        <div>
          <span class="inline-block px-3 py-1 bg-muted text-muted-foreground rounded-full text-xs mb-4">
            {{ product.category }}
          </span>
          <h1 class="text-3xl md:text-4xl mb-4">{{ product.name }}</h1>
          <p class="text-2xl text-primary mb-6">€{{ product.price }}</p>
          <p class="text-muted-foreground leading-relaxed mb-6">{{ product.description }}</p>

          <!-- Soonest availability -->
          <div v-if="product.soonest_availability" class="inline-flex items-center gap-2 text-sm text-muted-foreground mb-6">
            <ClockIcon class="w-4 h-4 flex-shrink-0" />
            <span>{{ tn('product.soonest_availability', product.soonest_availability) }}</span>
          </div>

          <!-- Online orderable: add to cart -->
          <template v-if="product.is_orderable_online">
            <div class="flex items-center gap-3">
              <div class="flex items-center border border-border rounded-md">
                <button
                  class="px-3 py-2 hover:bg-muted transition-colors"
                  :disabled="quantity <= 1"
                  :class="quantity <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                  @click="quantity > 1 && quantity--"
                >
                  <MinusIcon class="w-4 h-4" />
                </button>
                <input
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  class="w-14 text-center py-2 text-sm border-x border-border bg-background focus:outline-none"
                  @change="clampQuantity"
                />
                <button
                  class="px-3 py-2 hover:bg-muted transition-colors"
                  @click="quantity++"
                >
                  <PlusIcon class="w-4 h-4" />
                </button>
              </div>
              <button
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-colors"
                @click="addToCart"
              >
                <ShoppingCartIcon class="w-4 h-4" />
                {{ t('common.add_to_cart') }}
              </button>
            </div>
          </template>

          <!-- Special order form -->
          <template v-else>
            <div class="border border-border rounded-lg p-6 bg-card">
              <div class="flex items-center gap-2 mb-2">
                <ClipboardListIcon class="w-5 h-5 text-primary" />
                <h3 class="font-medium">{{ t('special_order.title') }}</h3>
              </div>
              <p class="text-sm text-muted-foreground mb-4">{{ t('special_order.description') }}</p>

              <div v-if="$page.props.flash?.success" class="p-3 mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ t($page.props.flash.success) }}
              </div>

              <form @submit.prevent="submitSpecialOrder" class="space-y-3">
                <div>
                  <label class="block text-sm font-medium mb-1">{{ t('special_order.your_name') }}</label>
                  <input
                    v-model="specialForm.customer_name"
                    type="text"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  />
                  <p v-if="specialForm.errors.customer_name" class="text-destructive text-xs mt-1">{{ specialForm.errors.customer_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">{{ t('special_order.your_email') }}</label>
                  <input
                    v-model="specialForm.customer_email"
                    type="email"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  />
                  <p v-if="specialForm.errors.customer_email" class="text-destructive text-xs mt-1">{{ specialForm.errors.customer_email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">{{ t('special_order.your_phone') }}</label>
                  <input
                    v-model="specialForm.customer_phone"
                    type="tel"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  />
                  <p v-if="specialForm.errors.customer_phone" class="text-destructive text-xs mt-1">{{ specialForm.errors.customer_phone }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1">{{ t('special_order.message') }} <span class="text-muted-foreground">({{ t('checkout.optional') }})</span></label>
                  <textarea
                    v-model="specialForm.message"
                    rows="3"
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary resize-none"
                  ></textarea>
                </div>
                <button
                  type="submit"
                  :disabled="specialForm.processing"
                  class="w-full px-4 py-2.5 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ specialForm.processing ? t('common.loading') : t('special_order.submit') }}
                </button>
              </form>
            </div>
          </template>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft as ArrowLeftIcon, Minus as MinusIcon, Plus as PlusIcon, ShoppingCart as ShoppingCartIcon, ClipboardList as ClipboardListIcon, Clock as ClockIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'
import { useRecaptcha } from '@/composables/useRecaptcha'

const { t, tn } = useTranslation()
const { localizedUrl } = useLocale()
const { execute: executeRecaptcha } = useRecaptcha()

const props = defineProps({
  product: Object,
})

const metaDescription = computed(() => {
  const desc = props.product.description || ''
  return desc.length > 160 ? desc.substring(0, 157) + '...' : desc
})

const selectedImage = ref(props.product.image)
const quantity = ref(1)
const cartItemsCount = computed(() => usePage().props.cartItemsCount)

function clampQuantity() {
  if (quantity.value < 1) quantity.value = 1
}

function addToCart() {
  router.post(localizedUrl('/cart'), { product_id: props.product.id, quantity: quantity.value }, { preserveScroll: true })
}

// Special order form
const user = computed(() => usePage().props.auth?.user)
const specialForm = useForm({
  product_id: props.product.id,
  customer_name: user.value?.name ?? '',
  customer_email: user.value?.email ?? '',
  customer_phone: user.value?.phone ?? '',
  message: '',
  recaptcha_token: '',
})

async function submitSpecialOrder() {
  specialForm.recaptcha_token = await executeRecaptcha('special_order')
  specialForm.post(localizedUrl('/special-order'), {
    preserveScroll: true,
    onSuccess: () => {
      specialForm.reset('message')
    },
  })
}
</script>
