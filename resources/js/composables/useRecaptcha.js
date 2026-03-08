import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

let scriptLoaded = false

export function useRecaptcha() {
  const siteKey = usePage().props.recaptchaSiteKey

  onMounted(() => {
    if (!siteKey || scriptLoaded) return
    if (document.querySelector(`script[src*="recaptcha"]`)) {
      scriptLoaded = true
      return
    }
    const script = document.createElement('script')
    script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`
    document.head.appendChild(script)
    scriptLoaded = true
  })

  function execute(action) {
    return new Promise((resolve) => {
      if (!siteKey || !window.grecaptcha) {
        resolve('')
        return
      }
      window.grecaptcha.ready(() => {
        window.grecaptcha.execute(siteKey, { action }).then(resolve)
      })
    })
  }

  return { execute }
}
