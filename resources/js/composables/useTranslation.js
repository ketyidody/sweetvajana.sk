import { usePage } from '@inertiajs/vue3'

export function useTranslation() {
  const page = usePage()

  function t(key, params = {}) {
    const parts = key.split('.')
    const group = parts[0]
    const k = parts.slice(1).join('.')

    const translations = page.props.translations || {}
    let value = translations[group]?.[k]

    if (!value) {
      return key
    }

    // Replace :param placeholders
    Object.entries(params).forEach(([param, val]) => {
      value = value.replace(new RegExp(`:${param}`, 'g'), val)
    })

    return value
  }

  return { t }
}
