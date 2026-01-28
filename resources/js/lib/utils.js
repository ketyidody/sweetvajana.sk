/**
 * Merge class names
 * @param  {...any} classes
 * @returns string
 */
export function cn(...classes) {
  return classes.filter(Boolean).join(' ')
}
