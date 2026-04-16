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
          <button
            v-if="!product.is_orderable_online && pricesPageContent"
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 mb-6 border border-border rounded-md text-sm font-medium hover:bg-muted transition-colors"
            @click="showPricesModal = true"
          >
            {{ t('special_order.view_prices') }}
          </button>
          <p v-if="product.is_orderable_online" class="text-2xl text-primary mb-6">€{{ product.price }}</p>
          <p v-if="product.is_orderable_online" class="text-muted-foreground leading-relaxed mb-6">{{ product.description }}</p>

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

              <form @submit.prevent="submitSpecialOrder" class="space-y-4">

                <!-- Corpus selector -->
                <div v-if="product.corpuses?.length">
                  <label class="block text-sm font-medium mb-1">
                    {{ t('special_order.corpus') }}
                    <span class="text-destructive ml-0.5">*</span>
                  </label>
                  <select
                    v-model="specialForm.corpus_id"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  >
                    <option :value="null" disabled>— {{ t('special_order.select_corpus') }} —</option>
                    <option v-for="corpus in product.corpuses" :key="corpus.id" :value="corpus.id">
                      {{ corpus.name }}
                    </option>
                  </select>
                  <p v-if="specialForm.errors.corpus_id" class="text-destructive text-xs mt-1">{{ specialForm.errors.corpus_id }}</p>
                </div>

                <!-- Cream Flavor selector -->
                <div v-if="product.cream_flavors?.length">
                  <label class="block text-sm font-medium mb-1">
                    {{ t('special_order.cream_flavor') }}
                    <span class="text-destructive ml-0.5">*</span>
                  </label>
                  <select
                    v-model="specialForm.cream_flavor_id"
                    required
                    class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  >
                    <option :value="null" disabled>— {{ t('special_order.select_cream_flavor') }} —</option>
                    <option v-for="flavor in product.cream_flavors" :key="flavor.id" :value="flavor.id">
                      {{ flavor.name }}
                    </option>
                  </select>
                  <p v-if="specialForm.errors.cream_flavor_id" class="text-destructive text-xs mt-1">{{ specialForm.errors.cream_flavor_id }}</p>
                </div>

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

  <!-- Prices modal -->
  <Teleport to="body">
    <Transition
      enter-active-class="ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showPricesModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="showPricesModal = false"
      >
        <div class="absolute inset-0 bg-black/50" @click="showPricesModal = false" />
        <div class="relative z-10 w-full max-w-xl max-h-[80vh] overflow-y-auto bg-card border border-border rounded-lg shadow-lg">
          <button
            type="button"
            class="absolute top-3 right-3 text-muted-foreground hover:text-foreground transition-colors"
            @click="showPricesModal = false"
          >
            <XIcon class="w-5 h-5" />
          </button>
          <div class="px-6 py-5 text-sm text-foreground prose prose-neutral max-w-none" v-html="pricesPageContent" />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ArrowLeft as ArrowLeftIcon, Minus as MinusIcon, Plus as PlusIcon, ShoppingCart as ShoppingCartIcon, ClipboardList as ClipboardListIcon, Clock as ClockIcon, X as XIcon } from 'lucide-vue-next'
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
  pricesPageContent: { type: String, default: null },
})

const metaDescription = computed(() => {
  const desc = props.product.description || ''
  return desc.length > 160 ? desc.substring(0, 157) + '...' : desc
})

const selectedImage = ref(props.product.image)
const quantity = ref(1)
const showPricesModal = ref(false)

function onKeyDown(e) {
  if (e.key === 'Escape' && showPricesModal.value) showPricesModal.value = false
}
onMounted(() => document.addEventListener('keydown', onKeyDown))
onUnmounted(() => document.removeEventListener('keydown', onKeyDown))
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
  corpus_id: null,
  cream_flavor_id: null,
  recaptcha_token: '',
})


async function submitSpecialOrder() {
  specialForm.recaptcha_token = await executeRecaptcha('special_order')
  specialForm.post(localizedUrl('/special-order'), {
    preserveScroll: true,
    onSuccess: () => {
      specialForm.reset('message', 'corpus_id', 'cream_flavor_id')
    },
  })
}
</script>
