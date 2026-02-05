import { usePage } from '@inertiajs/vue3'

export function useLocale() {
  const page = usePage()

  function currentLocale() {
    return page.props.locale || 'sk'
  }

  function defaultLocale() {
    return page.props.defaultLocale || 'sk'
  }

  function localizedUrl(path) {
    const locale = currentLocale()
    const defLocale = defaultLocale()

    if (locale === defLocale) {
      return path
    }

    // Ensure path starts with /
    const normalizedPath = path.startsWith('/') ? path : '/' + path
    return '/' + locale + (normalizedPath === '/' ? '' : normalizedPath)
  }

  function switchLocaleUrl(targetLocale) {
    const defLocale = defaultLocale()
    const current = currentLocale()

    // Get current path and strip existing locale prefix
    let path = window.location.pathname
    if (current !== defLocale) {
      path = path.replace(new RegExp(`^/${current}(/|$)`), '/')
    }

    // Add target locale prefix (skip for default)
    if (targetLocale === defLocale) {
      return path || '/'
    }

    return '/' + targetLocale + (path === '/' ? '' : path)
  }

  return { currentLocale, defaultLocale, localizedUrl, switchLocaleUrl }
}
