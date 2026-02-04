<template>
  <Head title="Contact" />
  <div class="min-h-screen flex flex-col bg-background">
    <Header :cart-items-count="cartItemsCount" @cart-click="() => router.visit('/cart')" />

    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-medium mb-4">Contact Us</h1>
        <p v-if="settings.contact_text" class="text-muted-foreground mb-8">
          {{ settings.contact_text }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Contact Form -->
          <div class="md:col-span-2">
            <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md text-green-800 text-sm">
              {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="bg-card rounded-lg border border-border p-6 space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium mb-1">Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
                <p v-if="form.errors.name" class="text-destructive text-sm mt-1">{{ form.errors.name }}</p>
              </div>

              <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
                <p v-if="form.errors.email" class="text-destructive text-sm mt-1">{{ form.errors.email }}</p>
              </div>

              <div>
                <label for="subject" class="block text-sm font-medium mb-1">Subject</label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  required
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
                <p v-if="form.errors.subject" class="text-destructive text-sm mt-1">{{ form.errors.subject }}</p>
              </div>

              <div>
                <label for="message" class="block text-sm font-medium mb-1">Message</label>
                <textarea
                  id="message"
                  v-model="form.message"
                  rows="5"
                  required
                  class="w-full px-3 py-2 text-sm border border-border rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary"
                />
                <p v-if="form.errors.message" class="text-destructive text-sm mt-1">{{ form.errors.message }}</p>
              </div>

              <p v-if="form.errors.recaptcha_token" class="text-destructive text-sm">{{ form.errors.recaptcha_token }}</p>

              <div>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90 disabled:opacity-50"
                >
                  Send Message
                </button>
              </div>
            </form>
          </div>

          <!-- Contact Info Sidebar -->
          <div class="space-y-6">
            <div class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">Get In Touch</h2>
              <div class="space-y-4">
                <a
                  v-if="settings.phone"
                  :href="'tel:' + settings.phone"
                  class="flex items-center gap-3 text-muted-foreground hover:text-foreground transition-colors"
                >
                  <PhoneIcon class="h-5 w-5 shrink-0" />
                  <span class="text-sm">{{ settings.phone }}</span>
                </a>
                <a
                  v-if="settings.email"
                  :href="'mailto:' + settings.email"
                  class="flex items-center gap-3 text-muted-foreground hover:text-foreground transition-colors"
                >
                  <MailIcon class="h-5 w-5 shrink-0" />
                  <span class="text-sm">{{ settings.email }}</span>
                </a>
              </div>
            </div>

            <div v-if="settings.instagram_url || settings.facebook_url" class="bg-card rounded-lg border border-border p-6">
              <h2 class="text-lg font-medium mb-4">Follow Us</h2>
              <div class="flex gap-4">
                <a
                  v-if="settings.instagram_url"
                  :href="settings.instagram_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-muted-foreground hover:text-foreground transition-colors"
                >
                  <InstagramIcon class="h-5 w-5" />
                </a>
                <a
                  v-if="settings.facebook_url"
                  :href="settings.facebook_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-muted-foreground hover:text-foreground transition-colors"
                >
                  <FacebookIcon class="h-5 w-5" />
                </a>
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
import { computed, onMounted } from 'vue'
import { Head, router, usePage, useForm } from '@inertiajs/vue3'
import { Phone as PhoneIcon, Mail as MailIcon, Instagram as InstagramIcon, Facebook as FacebookIcon } from 'lucide-vue-next'
import Header from '@/Components/Layout/Header.vue'
import Footer from '@/Components/Layout/Footer.vue'

const props = defineProps({
  recaptchaSiteKey: String,
})

const cartItemsCount = computed(() => usePage().props.cartItemsCount)
const settings = computed(() => usePage().props.site_settings || {})

const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: '',
  recaptcha_token: '',
})

onMounted(() => {
  if (props.recaptchaSiteKey) {
    const script = document.createElement('script')
    script.src = `https://www.google.com/recaptcha/api.js?render=${props.recaptchaSiteKey}`
    document.head.appendChild(script)
  }
})

function submit() {
  if (!props.recaptchaSiteKey) {
    form.post('/contact', { onSuccess: () => form.reset() })
    return
  }

  window.grecaptcha.ready(() => {
    window.grecaptcha.execute(props.recaptchaSiteKey, { action: 'contact' }).then((token) => {
      form.recaptcha_token = token
      form.post('/contact', { onSuccess: () => form.reset() })
    })
  })
}
</script>
