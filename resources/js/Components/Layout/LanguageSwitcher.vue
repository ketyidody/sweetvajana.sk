<template>
  <div class="relative" ref="wrapper">
    <button
      @click="open = !open"
      class="flex items-center gap-1.5 px-2 py-1.5 text-sm transition-colors duration-500 rounded-md"
      :class="transparent ? 'text-white hover:text-white/70' : 'text-foreground hover:text-foreground/70'"
    >
      <GlobeIcon class="w-4 h-4" />
      <span class="hidden sm:inline">{{ currentLang?.native_name }}</span>
      <ChevronDownIcon class="w-3 h-3" />
    </button>

    <div
      v-if="open"
      class="absolute right-0 mt-1 w-40 bg-card border border-border rounded-md shadow-lg py-1 z-50"
    >
      <a
        v-for="lang in languages"
        :key="lang.code"
        :href="switchLocaleUrl(lang.code)"
        class="block px-3 py-2 text-sm transition-colors"
        :class="lang.code === currentLocale() ? 'bg-muted text-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
        @click="onSwitch(lang.code, $event)"
      >
        {{ lang.native_name }}
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Globe as GlobeIcon, ChevronDown as ChevronDownIcon } from 'lucide-vue-next'
import { useLocale } from '@/composables/useLocale'

defineProps({
  transparent: {
    type: Boolean,
    default: false
  }
})

const { currentLocale, switchLocaleUrl } = useLocale()

const page = usePage()
const languages = computed(() => page.props.languages || [])
const currentLang = computed(() => languages.value.find(l => l.code === currentLocale()))
const isAuthenticated = computed(() => !!page.props.auth?.user)

const open = ref(false)
const wrapper = ref(null)

function onSwitch(langCode, event) {
  open.value = false

  if (isAuthenticated.value && langCode !== currentLocale()) {
    event.preventDefault()
    const url = switchLocaleUrl(langCode)
    router.patch('/locale', { locale: langCode }, {
      preserveState: false,
      onSuccess: () => {
        window.location.href = url
      },
    })
  }
}

function handleClickOutside(e) {
  if (wrapper.value && !wrapper.value.contains(e.target)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
