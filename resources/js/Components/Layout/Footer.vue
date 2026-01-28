<template>
  <footer id="contact" class="bg-muted border-t border-border mt-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- About -->
        <div>
          <h3 class="mb-4">Sweet Vajana</h3>
          <p class="text-muted-foreground">
            Handcrafted cakes and sweets made fresh daily with premium ingredients and lots of love.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="mb-4">Quick Links</h3>
          <nav class="space-y-2">
            <a href="#home" class="block text-muted-foreground hover:text-foreground transition-colors">
              Home
            </a>
            <a href="#products" class="block text-muted-foreground hover:text-foreground transition-colors">
              Products
            </a>
            <a href="#about" class="block text-muted-foreground hover:text-foreground transition-colors">
              About
            </a>
            <a href="#contact" class="block text-muted-foreground hover:text-foreground transition-colors">
              Contact
            </a>
          </nav>
        </div>

        <!-- Contact & Social -->
        <div>
          <h3 class="mb-4">Get In Touch</h3>
          <div class="space-y-3">
            <a href="tel:+1234567890" class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
              <PhoneIcon class="h-4 w-4" />
              <span>+1 (234) 567-890</span>
            </a>
            <a href="mailto:info@sweetvajana.sk" class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
              <MailIcon class="h-4 w-4" />
              <span>info@sweetvajana.sk</span>
            </a>
            <div class="flex gap-4 pt-2">
              <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                <InstagramIcon class="h-5 w-5" />
              </a>
              <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                <FacebookIcon class="h-5 w-5" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-border mt-8 pt-8 text-center text-muted-foreground">
        <p>&copy; {{ currentYear }} Sweet Vajana. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { Instagram as InstagramIcon, Facebook as FacebookIcon, Mail as MailIcon, Phone as PhoneIcon } from 'lucide-vue-next'

const currentYear = computed(() => new Date().getFullYear())
</script>
