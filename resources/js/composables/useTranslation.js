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

  // Pluralised translation: pipe-separated forms "one|few|many" (Slovak) or "one|other" (English)
  function tn(key, count, params = {}) {
    const raw = t(key, { ...params, count })
    const forms = raw.split('|')
    let form
    if (forms.length === 1) {
      form = forms[0]
    } else if (forms.length === 2) {
      form = count === 1 ? forms[0] : forms[1]
    } else {
      // 3-form Slavic pluralisation
      if (count === 1) form = forms[0]
      else if (count >= 2 && count <= 4) form = forms[1]
      else form = forms[2]
    }
    return form.replace(/:count/g, count)
  }

  return { t, tn }
}
