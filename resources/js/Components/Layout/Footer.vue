<template>
  <footer id="contact" class="bg-muted border-t border-border">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- About -->
        <div>
          <h3 class="mb-4">{{ settings.site_name || 'Sweet Vajana' }}</h3>
          <p class="text-muted-foreground">
            {{ settings.about_text || 'Handcrafted cakes and sweets made fresh daily with premium ingredients and lots of love.' }}
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="mb-4">{{ t('footer.quick_links') }}</h3>
          <nav class="space-y-2">
            <Link :href="localizedUrl('/')" class="block text-muted-foreground hover:text-foreground transition-colors">
              {{ t('nav.home') }}
            </Link>
            <Link :href="localizedUrl('/products')" class="block text-muted-foreground hover:text-foreground transition-colors">
              {{ t('nav.products') }}
            </Link>
            <Link :href="localizedUrl('/about')" class="block text-muted-foreground hover:text-foreground transition-colors">
              {{ t('nav.about') }}
            </Link>
            <Link :href="localizedUrl('/contact')" class="block text-muted-foreground hover:text-foreground transition-colors">
              {{ t('nav.contact') }}
            </Link>
          </nav>
        </div>

        <!-- Contact & Social -->
        <div>
          <h3 class="mb-4">{{ t('footer.get_in_touch') }}</h3>
          <div class="space-y-3">
            <a :href="'tel:' + (settings.phone || '+1234567890')" class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
              <PhoneIcon class="h-4 w-4" />
              <span>{{ settings.phone || '+1 (234) 567-890' }}</span>
            </a>
            <a :href="'mailto:' + (settings.email || 'info@sweetvajana.sk')" class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
              <MailIcon class="h-4 w-4" />
              <span>{{ settings.email || 'info@sweetvajana.sk' }}</span>
            </a>
            <div class="flex gap-4 pt-2">
              <a :href="settings.instagram_url || '#'" class="text-muted-foreground hover:text-foreground transition-colors">
                <InstagramIcon class="h-5 w-5" />
              </a>
              <a :href="settings.facebook_url || '#'" class="text-muted-foreground hover:text-foreground transition-colors">
                <FacebookIcon class="h-5 w-5" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-border mt-8 pt-8 text-center text-muted-foreground">
        <p>{{ settings.footer_text || `© ${currentYear} Sweet Vajana. ${t('footer.copyright')}` }}</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Instagram as InstagramIcon, Facebook as FacebookIcon, Mail as MailIcon, Phone as PhoneIcon } from 'lucide-vue-next'
import { useTranslation } from '@/composables/useTranslation'
import { useLocale } from '@/composables/useLocale'

const { t } = useTranslation()
const { localizedUrl } = useLocale()
const settings = computed(() => usePage().props.site_settings || {})
const currentYear = computed(() => new Date().getFullYear())
</script>
